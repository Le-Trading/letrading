<?php

namespace App\Controller;

use App\Entity\EtapeFormation;
use App\Entity\Formation;
use App\Entity\SectionFormation;
use App\Form\AdminEtapeType;
use App\Form\AdminFormationType;
use App\Form\AdminSectionType;
use App\Repository\EtapeFormationRepository;
use App\Repository\FormationRepository;
use App\Repository\SectionFormationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
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

    /**
     * Permet de visualiser les sections d'une formation
     *
     * @Route("/admin/formation/{id}/section", name="admin_formation_section_index")
     *
     * @return Response
     */
    public function indexSection(Formation $formation, SectionFormationRepository $repoFormation, EtapeFormationRepository $repoEtape){
        $sections = $repoFormation->findAll();
        $etapes = $repoEtape->findAll();

        return $this->render('admin/formation/section/index.html.twig',[
            'formation' => $formation,
            'sections' => $sections,
            'etapes' => $etapes
        ]);
    }

    /**
     * Permet de creer une section
     *
     * @Route("/admin/formation/{id}/section/create", name="admin_formation_section_create")
     *
     * @return Response
     */
    public function createSection(Formation $formation, Request $request, EntityManagerInterface $manager){
        $section = new SectionFormation();

        $form = $this->createForm(AdminSectionType::class, $section);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $section->setFormation($formation);
            $manager->persist($section);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre section a bien été créé !"
            );

            return $this->redirectToRoute('admin_formation_section_index', ['id' => $formation->getId()]);
        }

        return $this->render('admin/formation/section/create.html.twig', [
            'formation' => $formation,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet d'éditer une section
     *
     * @Route("/admin/formation/{idFormation}/section/{idSection}/edit", name="admin_formation_section_edit")
     * @ParamConverter("formation", options={"mapping": {"idFormation": "id"}})
     * @ParamConverter("section", options={"mapping": {"idSection": "id"}})
     *
     * @param Formation $formation
     * @param SectionFormation $section
     * @return Response
     */
    public function editSection(Formation $formation, SectionFormation $section, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(AdminSectionType::class, $section);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $manager->persist($section);
            $manager->flush();

            $this->addFlash(
                'success',
                "Le contenu de la formation {$section->getTitle()} à bien été modifié"
            );
        }
        return $this->render('admin/formation/section/edit.html.twig',[
            'formation' => $formation,
            'section' => $section,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de creer une étape
     *
     * @Route("/admin/formation/{idFormation}/section/{idSection}/etape/create", name="admin_formation_etape_create")
     * @ParamConverter("formation", options={"mapping": {"idFormation": "id"}})
     * @ParamConverter("section", options={"mapping": {"idSection": "id"}})
     *
     * @param Formation $formation
     * @param SectionFormation $section
     * @return Response
     */
    public function createEtape(SectionFormation $section, Formation $formation, Request $request, EntityManagerInterface $manager){
        $etape = new EtapeFormation();

        $form = $this->createForm(AdminEtapeType::class, $etape);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $etape->setSection($section);
            $manager->persist($etape);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre étape a bien été créé !"
            );

            return $this->redirectToRoute('admin_formation_section_index', ['id' => $formation->getId()]);
        }

        return $this->render('admin/formation/section/etape/create.html.twig', [
            'section' => $section,
            'formation' => $formation,
            'form' => $form->createView()
        ]);
    }

}
