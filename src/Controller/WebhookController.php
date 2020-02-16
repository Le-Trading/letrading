<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Entity\Souscription;
use App\Entity\StripeEventLog;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class WebhookController extends AbstractController
{
    /**
     * @var EntityManagerInterface
     */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/webhook/stripe", name="stripe_webhook")
     * @param Request $request
     * @param StripeClient $stripeClient
     * @return Response
     * @throws Exception
     */
    public function stripeWebhook(Request $request, StripeClient $stripeClient)
    {
        $data = json_decode($request->getContent(), true);
        if ($data === null) {
            throw new Exception('Bad JSON body from Stripe ');
        }
        $eventId = $data['id'];

        /**  VERIFICATION DE L'EVENT : DEJA HANDLED OU NON ? */
        $existingLog = $this->existingLog($eventId);
        if ($existingLog) {
            return new Response('Event previously handled');
        }
        $log = new StripeEventLog($eventId);
        $this->entityManager->persist($log);
        $this->entityManager->flush();


        $event = $stripeClient->findEvent($eventId);
        switch ($event->type) {
            case 'customer.subscription.deleted':
                $stripeSubscriptionId = $event->data->object->id;
                $souscription = $this->findSubscription($stripeSubscriptionId);
                $stripeClient->fullyCancelSubscription($souscription);
                break;
            case 'invoice.payment_succeeded':
                $stripeSubscriptionId = $event->data->object->subscription;
                if ($stripeSubscriptionId) {
                    $subscription = $this->findSubscription($stripeSubscriptionId);
                    $stripeSubscription = $stripeClient->findSubscription($stripeSubscriptionId);
                    $stripeClient->handleSubscriptionPaid($subscription, $stripeSubscription);
                }
                break;
            case 'invoice.payment_failed':
                $stripeSubscriptionId = $event->data->object->subscription;
                if ($stripeSubscriptionId) {
                    $subscription = $this->findSubscription($stripeSubscriptionId);
                    if ($event->data->object->attempt_count == 1) {
                        $stripeClient->fullyCancelSubscription($subscription);
                        $user = $subscription->getUser();
                        // todo - send the user an email about the problem
                    }
                    if ($event->data->object->attempt_count == 4) {
                        $stripeClient->fullyCancelSubscription($subscription);
                    }

                }
                break;
            default:
                throw new \Exception('Unexpected webhook type form Stripe! ' . $event->type);
        }
        return new Response('Event Handled: ' . $event->type);
    }

    /**
     * @param $stripeSubscriptionId
     * @return Souscription
     * @throws Exception
     */
    private function findSubscription($stripeSubscriptionId)
    {
        $subscription = $this->entityManager
            ->getRepository(Souscription::class)
            ->findOneBy([
                'stripeSubscriptionId' => $stripeSubscriptionId
            ]);
        if (!$subscription) {
            throw new \Exception('Aucune souscription trouvée pour l\'id suivant : ' . $stripeSubscriptionId);
        }
        return $subscription;
    }

    private function existingLog($eventId)
    {
        $existingLog = $this->entityManager->getRepository(StripeEventLog::class)
            ->findOneBy(['stripeEventId' => $eventId]);
        return $existingLog ? true : false;
    }
}
