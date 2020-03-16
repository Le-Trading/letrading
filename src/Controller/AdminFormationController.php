<?php

namespace App\Controller;

use App\Entity\Formation;
use App\Form\AdminFormationType;
use App\Repository\FormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class AdminFormationController
 * @package App\Controller
 */
class AdminFormationController extends AbstractController
{
    /**
     * @Route("/admin/formation", name="admin_formation_index")
     */
    public function index(FormationRepository $repo)
    {
        $formations = $repo->findAll();

        return $this->render('admin/formation/index.html.twig',
            ['formations' => $formations]
        );
    }

    /**
     * Permet de creer une formation
     *
     * @Route("/admin/formation/create", name="admin_formation_create")
     *
     * @return Response
     */
    public function create(Request $request, EntityManagerInterface $manager){
        $formation = new Formation();

        $form = $this->createForm(AdminFormationType::class, $formation);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $manager->persist($formation);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre formation a bien été créé !"
            );

            return $this->redirectToRoute('admin_formation_index');
        }

        return $this->render('admin/formation/create.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'éditer une formation
     *
     * @Route("/admin/formation/{id}/edit", name="admin_formation_edit")
     *
     * @return Response
     */
    public function edit(Formation $formation, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdminFormationType::class, $formation);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($formation);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le contenu de la formation {$formation->getTitle()} à bien été modifié"
            );
        }
        return $this->render('admin/formation/edit.html.twig',[
            'formation' => $formation,
            'form' => $form->createView()
        ]);
    }

}
