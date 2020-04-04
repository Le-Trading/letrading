<?php

namespace App\Controller;

use App\Entity\Offers;
use App\Entity\Temoignage;
use App\Form\AdminOfferType;
use App\Form\AdminTemoignageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminTemoignageController extends AbstractController
{
    /**
     * @Route("/admin/temoignages", name="admin_temoignage_index")
     */
    public function index()
    {
        $repo = $this->getDoctrine()->getRepository(Temoignage::class);
        $temoignages = $repo->createQueryBuilder('e')
            ->addOrderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->execute();

        return $this->render('admin/temoignage/index.html.twig',
            ['temoignages' => $temoignages ]
        );
    }

    /**
     * Permet d'afficher form d'édition
     *
     * @Route("/admin/temoignages/{id}/edit", name="admin_temoignage_edit")
     *
     * @param Temoignage $temoignage
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function edit(Temoignage $temoignage, Request $request, EntityManagerInterface $em){
        $form = $this->createForm(AdminTemoignageType::class, $temoignage);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            $em->persist($temoignage);
            $em->flush();

            $this->addFlash('success',
                "Le commentaire a bien été modifié");
        }

        return $this->render('admin/temoignage/edit.html.twig', [
            'temoignage' => $temoignage,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     *
     * @Route("/admin/temoignages/{id}/delete", name="admin_temoignage_delete")
     *
     * @param Temoignage $temoignage
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function delete(Temoignage $temoignage, EntityManagerInterface $em){
        $em->remove($temoignage);
        $em->flush();

        $this->addFlash('success',
            "Le témoignage a bien été supprimé");
        return $this->redirectToRoute('admin_temoignage_index');
    }
}
