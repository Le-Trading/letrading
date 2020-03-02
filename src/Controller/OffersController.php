<?php

namespace App\Controller;


use App\Client\StripeClient;
use App\Repository\OffersRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
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
    public function show($title, OffersRepository $repo, StripeClient $stripeClient)
    {
        $offer = $repo->findOneBy(['title' => $title]);
        $checkout = null;
        if(!$this->getUser()->ownThisOffer($offer)){
            if($offer->getType() == "charge")
                $checkout = $stripeClient->createCheckoutForCharge($offer, $this->getUser());
            else
                $checkout = $stripeClient->createCheckoutForSubscription($offer, $this->getUser());

        }

        return $this->render('offers/show.html.twig', [
            'offer' => $offer,
            'stripe_public_key' => $this->getParameter('stripe_public_key'),
            'CHECKOUT_SESSION_ID' => $checkout != null ? $checkout->id : null
        ]);
    }

}
