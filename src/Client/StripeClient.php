<?php

namespace App\Client;

use Doctrine\Persistence\ObjectManager;
use Stripe\Charge;
use Stripe\Exception\ApiErrorException;
use Stripe\Stripe;
use App\Entity\User;
use App\Entity\Souscription;
use Stripe\Customer;
use App\Entity\Offers;
use Stripe\Error\Base;
use App\Entity\Payment;
use Stripe\Subscription;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;

class StripeClient
{
    private $config;
    private $manager;
    private $logger;

    public function __construct($secretKey, array $config, EntityManagerInterface $manager, LoggerInterface $logger)
    {
        \Stripe\Stripe::setApiKey($secretKey);
        $this->config = $config;
        $this->manager = $manager;
        $this->logger = $logger;
    }

    public function createPremiumCharge(User $user, $token, Offers $offer)
    {
        $payment = new Payment();
        try {
            $customer = $this->createCustomer($user, $token);

            $charge = \Stripe\Charge::create([
                'amount' => $this->config['decimal'] ? $this->config['premium_amount'] * 100 : $this->config['premium_amount'],
                'currency' => $this->config['currency'],
                'customer' => $customer->id,
                'description' => 'Paiement de la formation premium'
            ]);

            $payment->setUser($user);
            $payment->setOffer($offer);
            $payment->setChargeId($charge->id);
            $this->manager->persist($payment);
            $this->manager->flush();

        } catch (\Stripe\Error\Base $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a premium payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);

            throw $e;
        }
    }

    public function createClassicSubscription(User $user, $token, Offers $offer)
    {

        try {
            $customer = $this->createCustomer($user, $token);
            $createSouscription = $this->createSubscription($user, $offer);
            $this->addSubscriptionToUser(
                $createSouscription,
                $user,
                $offer
            );

        } catch (\Stripe\Error\Base $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);

            throw $e;
        } catch (ApiErrorException $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
        }
    }

    public function createCustomer(User $user, $token)
    {
        if (!$user->getStripeCustomerId()) {
            $customer = \Stripe\Customer::create([
                'source' => $token,
                'description' => $user->getFullName(),
                'name' => $user->getFullName(),
                'email' => $user->getEmail()
            ]);
            $user->setStripeCustomerId($customer->id);
            $this->manager->persist($user);
            $this->manager->flush();
            return $customer;
        } else {
            $customer = \Stripe\Customer::retrieve($user->getStripeCustomerId());
            $customer->source = $token;
            $customer->save();
            return $customer;
        }
    }

    public function createSubscription(User $user, Offers $offer)
    {

        try {
            return \Stripe\Subscription::create([
                'customer' => $user->getStripeCustomerId(),
                'items' => [['plan' => $offer->getPlan()]]
            ]);
        } catch (ApiErrorException $e) {
        }
    }

    public function cancelSubscription(User $user)
    {
        $sub = \Stripe\Subscription::retrieve(
            $user->getSouscription()->getStripeSubscriptionId()
        );
        try {
            $sub->cancel_at_period_end = true;
            $sub->save();
        } catch (ApiErrorException $e) {
        }
    }

    public function reactivateSubscription (User $user){
        if (!$user->hasActiveSubscription()) {
            throw new \LogicException("Vous ne pouvez réactiver votre souscription que si elle ne s'est pas terminée auparavant.");
        }
        $souscription = \Stripe\Subscription::retrieve(
            $user->getSouscription()->getStripeSubscriptionId()
        );
        $souscription->plan = $user->getSouscription()->getOffer()->getPlan();
        $souscription->cancel_at_period_end = false;

        $souscription->save();
        return $souscription;
    }

    public function addSubscriptionToUser(\Stripe\Subscription $stripeSubscription, User $user, Offers $offer){
        $souscription = $user->getSouscription();
        if (!$souscription || $souscription == null) {
            $souscription = new Souscription();
            $souscription->setUser($user);
        }
        $periodEnd = \DateTime::createFromFormat('U', $stripeSubscription->current_period_end);
        $souscription->activateSubscription(
            $offer,
            $stripeSubscription->id,
            $periodEnd
        );

        $this->manager->persist($souscription);
        $this->manager->flush();
    }
}