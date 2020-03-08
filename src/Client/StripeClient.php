<?php

namespace App\Client;

use Doctrine\Persistence\ObjectManager;
use Stripe\Charge;
use Stripe\Checkout\Session;
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
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class StripeClient
{
    private $config;
    private $manager;
    private $logger;
    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function __construct($secretKey, array $config, EntityManagerInterface $manager, LoggerInterface $logger, UrlGeneratorInterface $router)
    {
        \Stripe\Stripe::setApiKey($secretKey);
        $this->config = $config;
        $this->manager = $manager;
        $this->logger = $logger;
        $this->router = $router;

    }

    /**
     * @param Offers $offer
     * @param User $user
     * @return Session
     * @throws ApiErrorException
     */
    public function createCheckoutForCharge(Offers $offer, User $user)
    {
        $urlHome = $this->router->generate(
            'homepage', [], UrlGeneratorInterface::ABSOLUTE_URL
        );
        $urlSuccess = $this->router->generate(
            'success_page', [], UrlGeneratorInterface::ABSOLUTE_URL
        );

        if ($user->getStripeCustomerId())
            $session = Session::create([
                'customer' => $user->getStripeCustomerId(),
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name' => $offer->getTitle(),
                    'amount' => $offer->getPrice(),
                    'currency' => 'eur',
                    'quantity' => 1,
                ]],
                'success_url' => $urlHome,
                'cancel_url' => $urlHome,
            ]);
        else
            $session = Session::create([
                'customer_email' => $user->getEmail(),
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'name' => $offer->getTitle(),
                    'amount' => $offer->getPrice(),
                    'currency' => 'eur',
                    'quantity' => 1,
                ]],
                'success_url' => $urlHome,
                'cancel_url' => $urlHome,
            ]);
        return $session;
    }

    /**
     * @param Offers $offer
     * @param User $user
     * @return Session
     * @throws ApiErrorException
     */
    public function createCheckoutForSubscription(Offers $offer, User $user)
    {
        $urlHome = $this->router->generate(
            'homepage', [], UrlGeneratorInterface::ABSOLUTE_URL
        );
        $urlSuccess = $this->router->generate(
            'success_page', [], UrlGeneratorInterface::ABSOLUTE_URL
        );
        if ($user->getStripeCustomerId())
            $session = Session::create([
                'customer' => $user->getStripeCustomerId(),
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'items' => [[
                        'plan' => $offer->getPlan(),
                    ]],
                ],
                'success_url' => $urlHome,
                'cancel_url' => $urlHome,
            ]);
        else
            $session = Session::create([
                'customer_email' => $user->getEmail(),
                'payment_method_types' => ['card'],
                'subscription_data' => [
                    'items' => [[
                        'plan' => $offer->getPlan(),
                    ]],
                ],
                'success_url' => $urlHome,
                'cancel_url' => $urlHome,
            ]);
        return $session;
    }

    /**
     * @param User $user
     * @throws ApiErrorException
     */
    public function cancelSubscription(User $user)
    {
        $sub = $this->findSubscription(
            $user->getSouscription()->getStripeSubscriptionId()
        );
        try {
            \Stripe\Subscription::update(
                $user->getSouscription()->getStripeSubscriptionId(),
                [
                    'cancel_at_period_end' => true,
                ]
            );
        } catch (\Stripe\Exception\ApiErrorException $e) {

        }
    }

    /**
     * @param User $user
     * @return Subscription
     */
    public function reactivateSubscription(User $user)
    {
        if (!$user->hasActiveSubscription()) {
            throw new \LogicException("Vous ne pouvez réactiver votre souscription que si elle ne s'est pas terminée auparavant.");
        }
        $stripeSubscriptionId = $user->getSouscription()->getStripeSubscriptionId();
        try {
            $subscription = \Stripe\Subscription::retrieve($stripeSubscriptionId);
        } catch (ApiErrorException $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);

        }
        try {
            \Stripe\Subscription::update($stripeSubscriptionId, [
                'cancel_at_period_end' => false,
                'items' => [
                    [
                        'id' => $subscription->items->data[0]->id,
                        'plan' => $user->getSouscription()->getOffer()->getPlan(),
                    ],
                ],
            ]);
        } catch (ApiErrorException $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
        }
        return $subscription;
    }

    /**
     * @param $idSubscriptionStripe
     * @param User $user
     * @param Offers $offer
     * @throws \Exception
     */
    public function addSubscriptionToUser($idSubscriptionStripe, User $user, Offers $offer)
    {
        $souscription = $user->getSouscription();
        if (!$souscription || $souscription == null) {
            $souscription = new Souscription();
            $souscription->setUser($user);
        }
        try {
            $periodEnd = \DateTime::createFromFormat('U', $this->findCurrentPeriodEnd($idSubscriptionStripe));
        } catch (ApiErrorException $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
        }
        $souscription->activateSubscription(
            $offer,
            $idSubscriptionStripe,
            $periodEnd
        );

        $this->manager->persist($souscription);
        $this->manager->flush();
    }

    /**
     * @param $eventId
     * @return \Stripe\Event
     * @throws \Exception
     */
    public function findEvent($eventId)
    {
        try {
            return \Stripe\Event::retrieve($eventId);
        } catch (ApiErrorException $e) {
            throw new \Exception('Aucun évènement trouvé : ' . $e);
        }
    }

    /**
     * @param Souscription $subscription
     */
    public function fullyCancelSubscription(Souscription $subscription)
    {
        $subscription->desactivateSubscription();
        $this->manager->persist($subscription);
        $this->manager->flush();
    }

    public function handleSubscriptionPaid(Souscription $subscription, \Stripe\Subscription $stripeSubscription)
    {
        $newPeriodEnd = \DateTime::createFromFormat('U', $stripeSubscription->current_period_end);

        $isRenewal = $newPeriodEnd > $subscription->getBillingPeriodEndsAt();

        $subscription->setBillingPeriodEndsAt($newPeriodEnd);
        $this->manager->persist($subscription);
        $this->manager->flush();
    }

    /**
     * @param $stripeSubscriptionId
     * @return Subscription
     * @throws ApiErrorException
     */
    public function findSubscription($stripeSubscriptionId)
    {
        try {
            $sub = \Stripe\Subscription::retrieve($stripeSubscriptionId);
            return $sub;
        } catch (\Stripe\Error\Base $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);

            throw $e;
        } catch (ApiErrorException $e) {
            $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
            throw $e;
        }
    }

    /**
     * @param $stripeSubscriptionId
     * @return int
     * @throws ApiErrorException
     */
    public function findCurrentPeriodEnd($stripeSubscriptionId)
    {
        $sub = \Stripe\Subscription::retrieve($stripeSubscriptionId);
        dump($sub);
        return $sub->current_period_end;
    }

    /**
     * @param $invoiceId
     * @return \Stripe\Invoice
     * @throws ApiErrorException
     */
    public function findInvoice($invoiceId)
    {
        return \Stripe\Invoice::retrieve($invoiceId);
    }

    /**
     * @param User $user
     * @return \Stripe\Invoice[]
     * @throws ApiErrorException
     */
    public function findPaidInvoices(User $user)
    {
        $allInvoices = \Stripe\Invoice::all([
            'customer' => $user->getStripeCustomerId()
        ]);
        $iterator = $allInvoices->autoPagingIterator();
        $invoices = [];
        foreach ($iterator as $invoice) {
            if ($invoice->paid) {
                $invoices[] = $invoice;
            }
        }
        dump($invoices);
        return $invoices;
    }

    /**
     * @param User $user
     * @return Session
     * @throws ApiErrorException
     */
    function updateCustomerCard(User $user)
    {
        $urlHome = $this->router->generate(
            'manage_souscription', [], UrlGeneratorInterface::ABSOLUTE_URL
        );
        $session = Session::create([
            'customer_email' => $user->getEmail(),
            'payment_method_types' => ['card'],
            'mode' => 'setup',
            'setup_intent_data' => [
                'metadata' => [
                    'customer_id' => $user->getStripeCustomerId(),
                    'subscription_id' => $user->getSouscription()->getStripeSubscriptionId(),
                ],
            ],
            'success_url' => 'http://localhost:8000/success' . '/{CHECKOUT_SESSION_ID}',
            'cancel_url' => $urlHome,
        ]);
        return $session;
    }

    /**
     * @param $id
     * @param User $user
     * @throws ApiErrorException
     */
    public function handleChangementCardSession($id, User $user)
    {
        $retrieve = Session::retrieve($id);
        $setupIntent = \Stripe\SetupIntent::retrieve($retrieve->setup_intent);
        $payment_method = \Stripe\PaymentMethod::retrieve($setupIntent->payment_method);
        $payment_method->attach(['customer' => $user->getStripeCustomerId()]);
        \Stripe\Customer::update(
            $user->getStripeCustomerId(),
            [
                'invoice_settings' => ['default_payment_method' => $setupIntent->payment_method],
            ]
        );
        \Stripe\Subscription::update(
            $user->getSouscription()->getStripeSubscriptionId(),
            [
                'default_payment_method' => $setupIntent->payment_method,
            ]
        );
    }
}
