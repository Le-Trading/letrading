<?php

namespace App\Controller;

use Stripe\Charge;
use Stripe\Stripe;
use App\Entity\Offers;
use App\Entity\Payment;
use App\Client\StripeClient;
use App\Repository\OffersRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class OffersController extends AbstractController
{
    /**
     * Affiche les annonces
     *
     * @Route("/offers", name="offers_index")
     */
    public function index(OffersRepository $repo)
    {
        $offers = $repo->findAll();

        return $this->render('offers/index.html.twig', [
            'offers' => $offers
        ]);
    }

    /**
     * Affiche le détail de l'annonce
     *
     * @Route("/offers/{title}", name="offers_show")
     *
     * @param $title
     * @param OffersRepository $repo
     * @return Response
     */
    public function show($title, OffersRepository $repo)
    {
        $offers = $repo->findByTitle($title);
        return $this->render('offers/show.html.twig', [
            'offers' => $offers
        ]);
    }

    /**
     * @Route("/offers/{title}/prepare", name="order_prepare", schemes={"%secure_channel%"})
     * @param Request $request
     * @param Offers $offer
     * @param StripeClient $stripe
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|Response
     */
    public function prepareAction(Request $request, Offers $offer, StripeClient $stripe)
    {
        if ($this->getUser()) {

            if (!$this->getUser()->ownThisOffer($offer)) {


                $form = $this->get('form.factory')
                    ->createNamedBuilder('payment-form')
                    ->add('token', HiddenType::class, [
                        'constraints' => [new NotBlank()],
                    ])
                    ->add('valider', SubmitType::class)
                    ->getForm();

                if ($request->isMethod('POST')) {
                    $form->handleRequest($request);

                    if ($form->isValid()) {
                        if ($offer->getType() == "charge") {
                            try {
                                $stripe->createPremiumCharge($this->getUser(), $form->get('token')->getData(), $offer);

                                $this->addFlash(
                                    'success',
                                    "Votre formation a bien été payée !"
                                );
                            } catch (\Stripe\Error\Base $e) {
                                $this->addFlash(
                                    'warning',
                                    "Votre paiement n'a pas pu être effectué"
                                );
                            } finally {
                                return $this->redirectToRoute('offers_index');
                            }
                        } else if ($offer->getType() == "subscription") {
                            try {
                                $stripe->createClassicSubscription($this->getUser(), $form->get('token')->getData(), $offer);

                                $this->addFlash(
                                    'success',
                                    "Votre abonnement a bien été pris en compte !"
                                );
                            } catch (\Stripe\Error\Base $e) {
                                $this->addFlash(
                                    'warning',
                                    "Votre paiement n'a pas pu être effectué"
                                );
                            } finally {
                                return $this->redirectToRoute('offers_index');
                            }
                        } else {
                            return $this->redirectToRoute('homepage');
                        }
                    }
                }

                return $this->render('offers/prepare.html.twig', [
                    'offer' => $offer,
                    'form' => $form->createView(),
                    'stripe_public_key' => $this->getParameter('stripe_public_key'),
                ]);
            } else {
                $this->addFlash(
                    'warning',
                    "Vous possédez déjà cette offre."
                );
                return $this->redirectToRoute("offers_index");
            }

        } else {
            $this->addFlash(
                'warning',
                "Merci de vous connecter ou bien vous créer un compte pour avoir accès à nos offres"
            );
            return $this->redirectToRoute('account_login');
        }
    }
}
