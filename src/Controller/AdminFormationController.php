<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminFormationController extends AbstractController
{
    /**
     * @Route("/admin/formation", name="admin_formation_index")
     */
    public function index()
    {
        return $this->render('admin/formation/index.html.twig');
    }
}
