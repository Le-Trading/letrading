<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Repository\NotifRepository;
use App\Repository\OffersRepository;
use App\Service\ContactService;
use App\Repository\ThreadRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(OffersRepository $repo)
    {
        $offers = $repo->findAll();
        return $this->render('home.html.twig',
            ['offers' => $offers]
        );
    }

    /**
     * Formulaire de contact
     *
     * @Route("/contact-us", name="pages_contact")
     * 
     * @return Response
     */
    public function contactPage(Request $request, ContactService $contactService){
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $contactService->notify($contact);

            $this->addFlash(
                'success',
                "Votre email a bien été envoyé !"
            );
            return $this->redirectToRoute('homepage');
        }

        return $this->render('pages/contact.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Recuperation des threads pour affichage header
     */
    public function getThreads(ThreadRepository $repo){
        $threads = $repo->findAll();
        return $this->render(
            'partials/request/thread.html.twig',
            ['threads' => $threads]
        );
    }

    /**
     * Recuperation des notifs
     */
    public function getNotifs(NotifRepository $repo){
        $notifs = $repo->findBy([
            'receiver' => $this->getUser()
        ]);
        $nbNonLu = $repo->findBy([
            'receiver' => $this->getUser(),
            'checked' => 0
        ]);
        return $this->render('partials/request/notifs.html.twig',
            ['notifs' => $notifs, 'notifNonLu' => count($nbNonLu)]
        );
    }
}
