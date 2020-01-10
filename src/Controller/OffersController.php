<?php

namespace App\Controller;

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
     * @return Response
     */
    public function show($title, OffersRepository $repo){
        $offers = $repo->findByTitle($title);
        return $this->render('offers/show.html.twig', [
            'offers' => $offers
        ]);
    }
}
