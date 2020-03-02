<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HistoricController extends AbstractController
{
    /**
     * @Route("/historic", name="historic_index")
     */
    public function index(EntityManagerInterface $manager)
    {
        //recuperation des trades
        $trades = $manager->createQuery(
            'SELECT p,t 
                    FROM App\Entity\Post p, App\Entity\Thread t 
                    WHERE p.feeling is not null
                    and p.Thread = t.id
                    ')
            ->getScalarResult();

        return $this->render('historic/index.html.twig',
            ['trades' => $trades]
        );
    }
}
