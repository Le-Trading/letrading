<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Entity\Contact;
use App\Entity\Notif;
use App\Form\ContactType;
use App\Repository\NotifRepository;
use App\Repository\OffersRepository;
use App\Repository\ThreadRepository;
use App\Service\MailingService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    public function contactPage(Request $request, MailingService $mailingService){
        $contact = new Contact();

        $form = $this->createForm(ContactType::class, $contact);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $mailingService->contactSend($contact);

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
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function successParameter($checkout_session_id, StripeClient $stripeClient)
    {

        $stripeClient->handleChangementCardSession($checkout_session_id, $this->getUser());
        return $this->redirectToRoute('manage_souscription');
    }

    /**
     * Fonction qui met les notifs a lu
     *
     * @Route("/updateNotif", name="notifs_update")
     */
    public function updateNotif(EntityManagerInterface $manager, Request $request, NotifRepository $notifRepo){
        if($request->isXmlHttpRequest()) {
            $idNotif = $request->request->get('id');

            $notif = $notifRepo->findOneBy([
                'id' => $idNotif
            ]);
            $notif->setChecked(1);

            $manager->persist($notif);
            $manager->flush();

            return $this->json(['code' => 200, 'message' => 'commentaire lu'], 200);
        }
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
