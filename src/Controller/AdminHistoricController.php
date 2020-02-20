<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminHistoricController extends AbstractController
{
    /**
     * @Route("/admin/historic", name="admin_historic")
     */
    public function index(EntityManagerInterface $manager)
    {
        $trades = $manager->createQuery(
            'SELECT p,t 
                    FROM App\Entity\Post p, App\Entity\Thread t 
                    WHERE p.feeling is not null
                    and p.Thread = t.id
                    ')
            ->getScalarResult();

        return $this->render('admin/historic/index.html.twig',
            ['trades' => $trades]
        );
    }
}
