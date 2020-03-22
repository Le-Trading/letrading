<?php

namespace App\Controller;

use App\Repository\EtapeFormationRepository;
use App\Repository\FormationRepository;
use App\Repository\SectionFormationRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FormationController extends AbstractController
{
    /**
     * @Route("/formation", name="formation_index")
     */
    public function index(FormationRepository $repoFormation, SectionFormationRepository $repoSection, EtapeFormationRepository $repoEtape)
    {
        $formations = $repoFormation->findAll();
        $sections = $repoSection->findBy(['formation' => $formations]);
        $etapes = $repoEtape->findBy(
            ['section' => $sections],
            ['position' => 'asc']
        );

        return $this->render('formation/index.html.twig',
            [
                'formation' => $formations[0],
                'sections' => $sections,
                'etapes' => $etapes
            ]
        );
    }
}
