<?php
 
namespace App\Client;
 
use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\User;
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
      $customer = \Stripe\Customer::create([
        'source' => $token,
        'description' => $user->getFullName(),
        'name' => $user->getFullName(),
        'email' => $user->getEmail()
      ]);

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

  public function createClassicSubscription(User $user, $token, Offers $offer){
    $payment = new Payment();

    try {
      $customer = \Stripe\Customer::create([
        'source' => $token,
        'description' => $user->getFullName(),
        'name' => $user->getFullName(),
        'email' => $user->getEmail()
      ]);

      $subscription = \Stripe\Subscription::create([
        'customer' => $customer->id,
        'items' => [['plan' => 'premium']]
      ]);

      $payment->setUser($user);
      $payment->setOffer($offer);
      $payment->setChargeId($subscription->id);
      $this->manager->persist($payment);
      $this->manager->flush();

    } catch (\Stripe\Error\Base $e) {
      $this->logger->error(sprintf('%s exception encountered when creating a classic payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
 
      throw $e;
    }
  }

}