<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Entity\Offers;
use App\Entity\Payment;
use App\Entity\Souscription;
use App\Entity\StripeEventLog;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
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
    private $logger;

    public function __construct(EntityManagerInterface $entityManager, LoggerInterface $logger)
    {
        $this->entityManager = $entityManager;
        $this->logger = $logger;

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
        $this->logger->info("info : ", ['data' => $data]);

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
        $object = $event->data->object;
        switch ($event->type) {
            case 'checkout.session.completed':
                $user = $this->findUserBy('stripeCustomerId', $object->customer) != null ?
                    $this->findUserBy('stripeCustomerId', $object->customer) : $this->findUserBy('email', $object->customer_email);
                if(!$user->getStripeCustomerId()){
                    $user->setStripeCustomerId($object->customer);
                }
                $offer = isset($object->display_items[0]->custom->name) ?
                    $this->findOfferBy('title', $object->display_items[0]->custom->name) : $this->findOfferBy('plan', $object->display_items[0]->plan->id);


                if($offer->getType() == "charge"){
                    $payment = new Payment();
                    $payment->setUser($user)
                        ->setOffer($offer)
                        ->setChargeId($event->data->object->id)
                        ->setCreatedAt(new \DateTime());
                    $this->entityManager->persist($payment);
                }
                else{
                    $stripeClient->addSubscriptionToUser(
                        $object->subscription,
                        $user,
                        $offer
                    );
                }
                $this->entityManager->persist($user);
                $this->entityManager->flush();
                break;
            case 'customer.subscription.deleted':
                $stripeSubscriptionId = $event->data->object->id;
                $souscription = $this->findSubscription($stripeSubscriptionId);
                $stripeClient->fullyCancelSubscription($souscription);
                break;

            case 'invoice.payment_succeeded':
                $stripeSubscriptionId = $object->subscription;
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
//                    if ($event->data->object->attempt_count == 1) {
//                        $stripeClient->fullyCancelSubscription($subscription);
//                        $user = $subscription->getUser();
//                        // todo - send the user an email about the problem
//                    }
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
    /**
     * @param $stripeSubscriptionId
     * @return User|object
     * @throws Exception
     */
    private function findUserBy($column, $data)
    {
        $user = $this->entityManager
            ->getRepository(User::class)
            ->findOneBy([
                $column => $data
            ]);
//        if (!$user) {
//            throw new \Exception('Aucun utilisateur trouvé pour le paiement avec la column ' . $column . ' suivante : ' . $email);
//        }
        return $user;
    }
    private function findOfferBy($column, $data)
    {
        $offer = $this->entityManager
            ->getRepository(Offers::class)
            ->findOneBy([
                $column => $data
            ]);
//        if (!$offer) {
//            throw new \Exception('Aucune offre trouvé pour l\'offre avec le titre suivant : ' . $title);
//        }
        return $offer;
    }
    private function existingLog($eventId)
    {
        $existingLog = $this->entityManager->getRepository(StripeEventLog::class)
            ->findOneBy(['stripeEventId' => $eventId]);
        return $existingLog ? true : false;
    }
}
