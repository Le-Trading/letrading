<?php

namespace App\Controller;

use App\Entity\Offers;
use App\Form\AdminOfferType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminOfferController extends AbstractController
{
    /**
     * @Route("/admin/offer", name="admin_offer")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Offers::class);
        $offers = $repo->findAll();

        return $this->render('admin/offers/index.html.twig',
            ['offers' => $offers ]
        );
    }

    /**
     * Permet de modifier une offre
     *
     * @Route("/admin/offer/{id}/edit", name="admin_offer_edit")
     * @param Offers $offer
     * @return Response
     */
    public function edit(Offers $offer, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdminOfferType::class, $offer);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($offer);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le contenu de l'offre {$offer->getTitle()} à bien été moddifié"
            );
        }
        return $this->render('admin/offers/edit.html.twig',[
            'offer' => $offer,
            'form' => $form->createView()
        ]);
    }
}
