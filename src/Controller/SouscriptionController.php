<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Repository\SouscriptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class SouscriptionController extends AbstractController
{
    /**
     * @Route("/account/souscription", name="manage_souscription")
     */
    public function index()
    {
        $user = $this->getUser();
        return $this->render('souscription/index.html.twig', [
            'user' => $user
        ]);
    }

    /**
     * @Route("/account/souscription/cancel", name="cancel_souscription")
     * @param StripeClient $stripe
     * @param EntityManagerInterface $em
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function cancel(StripeClient $stripe, EntityManagerInterface $em){
        $user = $this->getUser();
        $stripe->cancelSubscription($user);
        $souscription = $user->getSouscription();
        $souscription->desactivateSubscription();
        $em->persist($souscription);
        $em->flush();
        $this->addFlash('success', 'Votre abonnement a bien été annulé.');
        return $this->redirectToRoute('manage_souscription');
    }

    /**
     * @Route("/account/souscription/reactivate", name="reactivate_souscription")
     * @param StripeClient $stripeClient
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function reactivateSubscription(StripeClient $stripeClient){
        $stripeSouscription = $stripeClient->reactivateSubscription($this->getUser());
        $stripeClient->addSubscriptionToUser($stripeSouscription, $this->getUser(), $this->getUser()->getSouscription()->getOffer());
        $this->addFlash('success', 'Votre abonnement a bien été réactivé.');
        return $this->redirectToRoute('manage_souscription');
    }
}
