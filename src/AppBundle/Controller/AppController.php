<?php

namespace AppBundle\Controller;

use AppBundle\Entity\CompteReservation;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Entity\Reservation;
use AppBundle\Entity\Billet;
use AppBundle\Form\ReservationType;
use AppBundle\Event\ReservationEvent;

class AppController extends Controller
{
    public function indexAction()
    {
        return $this->render('AppBundle:App:index.html.twig');
    }

    public function tarifsAction()
    {
        return $this->render('AppBundle:App:tarifs.html.twig');
    }

    public function reservationAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // Création d'une instance réservation contenant une instance de billet
        $reservation = new Reservation();
        $reservation->addBillet(new Billet());

        // On récuperer laliste des dates où le musée a atteint le maximum de place
        $listDatesCompletes = $em->getRepository('AppBundle:CompteReservation')->findBy(array('total' => 1000));

        $form = $this->createForm(ReservationType::class, $reservation);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {

            // on appelle le service pour calculer le prix
            $calculateur = $this->get('calcul_prix.calcul_prix');
            $calculateur->calculPrix($reservation);

            // On attribut un code unique à la réservation, on persiste et on redirige vers la page confirmation
            $reservation->setCodeReservation(uniqid());
            $em->persist($reservation);
            $em->flush();

            return $this->redirectToRoute('app_confirmation', array('id' => $reservation->getId()));
        }

        return $this->render('AppBundle:App:reservation.html.twig', array('form' => $form->createView(), 'listDatesCompletes' => $listDatesCompletes));
    }

    public function confirmationAction(Reservation $reservation)
    {

        return $this->render('AppBundle:App:confirmation.html.twig', array('reservation' => $reservation));
    }

    public function doneAction(Request $request, Reservation $reservation)
    {
        try{
            // On récupere le token du paiement et crée un paiement stripe
            \Stripe\Stripe::setApiKey($this->container->getParameter('secret_key'));
            $customer = \Stripe\Customer::create(array(
                'email' => $request->request->get('stripeEmail'),
                'source'  => $request->request->get('stripeToken')
            ));

            $charge = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount'   => $reservation->getPriceFormated(),
                'currency' => 'eur'
            ));
        }catch (\Exception $e){

        }
        // On s'assure que le paiement est passé
        if($charge->paid){
            $em = $this->getDoctrine()->getManager();

            // On passe le status de la réservation à payer
            $reservation->addPayement();

            // On récupere une instance de CompteReservation correspondant à la date de la réservation
            $dateReservation = $em->getRepository('AppBundle:CompteReservation')->findOneBy(array('dateReservation' => $reservation->getDateReservation()));
            // Si $dateReservation n'existe pas, on crée une instance puis on lui ajoute la quantité de billets de la réservation
            // Sinon on ajoute simplement le nombre de billets de la réservation
            if ($dateReservation === null) {
                $dateReservation = new CompteReservation();
                $dateReservation->setDateReservation($reservation->getDateReservation());
                $dateReservation->setTotal($reservation->getBillets()->count());
            } else {
                $dateReservation->setTotal($reservation->getBillets()->count());
            }
            $em->persist($dateReservation);
            $em->flush();

            // On appelle l'event qui gerer l'envoie des billets
            $this->get('event_dispatcher')->dispatch('reservation.captured', new ReservationEvent($reservation));

            return $this->render('AppBundle:App:payer.html.twig', array('reservation' => $reservation));

        }else{
            // Si le paiement n'est pas passé, on redirige vers le paiement
            return $this->redirectToRoute('app_confirmatione', array('id' => $reservation->getId()));
        }
    }
}
