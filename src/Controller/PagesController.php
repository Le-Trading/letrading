<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Form\ContactType;
use App\Service\ContactService;
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
}
