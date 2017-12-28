<?php


namespace AppBundle\EventListener;


use AppBundle\Payment\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\Routing\RouterInterface;

class PaymentListener
{
    private $payment;
    private $em;
    /**
     * @var Router
     */
    private $router;

    public function __construct(StripePayment $payment, EntityManagerInterface $entityManager, RouterInterface $router)
    {
        $this->payment = $payment;
        $this->em = $entityManager;
        $this->router = $router;
    }

    public function processingPayment(GetResponseEvent $responseEvent)
    {
        $request = $responseEvent->getRequest();
        if($request->get('_route') == 'app_confirmation' && $request->getMethod() == 'POST') {
            $id = $request->get('id');
            $reservation = $this->em->getRepository("AppBundle:Reservation")->findOneBy(['id' => $id]);
            if(!$reservation->isPayer()) {
                $emailStripe = $request->request->get("stripeEmail");
                $stripeToken = $request->request->get("stripeToken");
                $this->payment->payed($emailStripe, $stripeToken, $reservation);

                $responseEvent->setResponse(new RedirectResponse($this->router->generate('app_done', ['id' => $id])));
            }
        }
    }
}