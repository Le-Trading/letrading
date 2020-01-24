<?php
 
namespace App\Client;
 
use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\User;
use Stripe\Error\Base;
use Stripe\Subscription;
use Psr\Log\LoggerInterface;
use Doctrine\ORM\EntityManagerInterface;
 
class StripeClient
{
  private $config;
  private $em;
  private $logger;
 
  public function __construct($secretKey, array $config, EntityManagerInterface $em, LoggerInterface $logger)
  {
    \Stripe\Stripe::setApiKey($secretKey);
    $this->config = $config;
    $this->em = $em;
    $this->logger = $logger;
  }
 
  public function createPremiumCharge(User $user, $token)
  {
    try {
      $charge = \Stripe\Charge::create([
        'amount' => $this->config['decimal'] ? $this->config['premium_amount'] * 100 : $this->config['premium_amount'],
        'currency' => $this->config['currency'],
        'description' => 'Paiement de la formation premium',
        'source' => $token,
        'receipt_email' => $user->getEmail(),
      ]);
    } catch (\Stripe\Error\Base $e) {
      $this->logger->error(sprintf('%s exception encountered when creating a premium payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
 
      throw $e;
    }
 
    $user->setChargeId($charge->id);
    $this->em->flush();
  }

  public function createClassicSubscription(User $user, $token){
    try {
      $charge = \Stripe\Subscription::create([
        'amount' => $this->config['decimal'] ? $this->config['classic_amount'] * 100 : $this->config['classic_amount'],
        'currency' => $this->config['currency'],
        'description' => 'Paiement de la formation classic',
        'source' => $token,
        'receipt_email' => $user->getEmail(),
        'items' => [['plan' => 'premium']]
      ]);
    } catch (\Stripe\Error\Base $e) {
      $this->logger->error(sprintf('%s exception encountered when creating a premium payment: "%s"', get_class($e), $e->getMessage()), ['exception' => $e]);
 
      throw $e;
    }
 
    $user->setChargeId($charge->id);
    $this->em->flush();
  }

}