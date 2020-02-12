<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Form\PaiementCBType;
use App\Repository\SouscriptionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;

class SouscriptionController extends AbstractController
{
    /**
     * @Route("/account/souscription", name="manage_souscription")
     * @param StripeClient $stripeClient
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function index(StripeClient $stripeClient, Request $request)
    {
        $user = $this->getUser();
        $form = $this->createForm(PaiementCBType::class);

        if ($request->isMethod('POST')) {
            $form->handleRequest($request);

            if ($form->isValid()) {
                try {
                    $stripeClient->updateCustomerCard($this->getUser(), $form->get('token')->getData());
                    $this->addFlash(
                        'success',
                        "Votre carte a bien été modifiée."
                    );
                } catch (\Stripe\Error\Card $e) {
                    $this->addFlash(
                        'warning',
                        "Il y a eu un problème avec l'enregistrement de votre nouvelle carte."
                    );
                }
                finally {
                    return $this->redirectToRoute('manage_souscription');
                }
            }
        }

        return $this->render('souscription/index.html.twig', [
            'form' => $form->createView(),
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
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
