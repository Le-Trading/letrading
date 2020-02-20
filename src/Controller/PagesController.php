<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\ContactService;
use App\Repository\ThreadRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PagesController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index()
    {
        return $this->render('home.html.twig');
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
     * @Route("/success", name="success_page")
     */
    public function success()
    {

        return $this->render('/pages/success.html.twig');
    }

    /**
     * @Route("/success/{checkout_session_id}", name="success_page_parameter")
     * @param $checkout_session_id
     * @param StripeClient $stripeClient
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function successParameter($checkout_session_id, StripeClient $stripeClient)
    {
        $stripeClient->handleChangementCardSession($checkout_session_id, $this->getUser());
        return $this->redirectToRoute('manage_souscription');
    }
}
