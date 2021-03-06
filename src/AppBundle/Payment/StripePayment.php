<?php


namespace AppBundle\Payment;


use AppBundle\Entity\Reservation;
use AppBundle\Event\ReservationEvent;
use AppBundle\EventListener\ReservationSubscriber;
use AppBundle\Mailer\MailGunMailer;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

class StripePayment
{
    private $stripeKey;
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    private $mailgun;

    public function __construct($stripeKey, EntityManagerInterface $entityManager, EventDispatcherInterface $eventDispatcher, MailGunMailer $mailGunMailer)
    {
        $this->stripeKey = $stripeKey;
        $this->entityManager = $entityManager;
        $this->eventDispatcher = $eventDispatcher;
        $this->mailgun = $mailGunMailer;
    }

    public function payed($stripeEmail, $stripeToken, Reservation $reservation)
    {
        try {
            // On récupere le token du paiement et crée un paiement stripe
            \Stripe\Stripe::setApiKey($this->stripeKey);
            $customer = \Stripe\Customer::create(array(
                'email' => $stripeEmail,
                'source' => $stripeToken
            ));

            $charge = \Stripe\Charge::create(array(
                'customer' => $customer->id,
                'amount' => $reservation->getPriceFormated(),
                'currency' => 'eur'
            ));
        } catch (\Exception $e) {
            return $e;
        }
        // On s'assure que le paiement est passé
        if (isset($charge) && $charge->paid) {
            // On passe le status de la réservation à payer
            $reservation->addPayement();

            $this->entityManager->persist($reservation);
            $this->entityManager->flush();

            $this->mailgun->sendingMessage($reservation);

            return $reservation;

        } else {
            // Si le paiement n'est pas passé, on redirige vers le paiement
            return $reservation;
        }
    }
}