<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Entity\Comment;
use App\Entity\Contact;
use App\Entity\Notif;
use App\Entity\Temoignage;
use App\Form\CommentType;
use App\Form\ContactType;
use App\Form\TemoignageType;
use App\Repository\MessageRepository;
use App\Repository\NotifRepository;
use App\Repository\OffersRepository;
use App\Repository\TemoignageRepository;
use App\Repository\ThreadRepository;
use App\Service\MailingService;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
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
     * @param Request $request
     * @param MailingService $mailingService
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
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
     * @Route("/temoignages", name="temoignages_page")
     * @param TemoignageRepository $repo
     * @param EntityManagerInterface $em
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @return Response
     */
    public function temoignages(TemoignageRepository $repo, EntityManagerInterface $em, PaginatorInterface $paginator, Request $request)
    {
        $query = $repo->createQueryBuilder('e')
        ->addOrderBy('e.createdAt', 'DESC')
        ->getQuery()
        ->execute();
        $temoignages = $paginator->paginate(
            $query,
            $request->query->getInt('page', 1),
            9
        );
        $temoignage = new Temoignage();

        $form = $this->createForm(TemoignageType::class, $temoignage);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $temoignage->setAuthor($this->getUser());
            $em->persist($temoignage);
            $em->flush();

            $this->addFlash(
                'success',
                "Votre témoignage a bien été pris en compte"
            );
            $query = $repo->createQueryBuilder('e')
                ->addOrderBy('e.createdAt', 'DESC')
                ->getQuery()
                ->execute();
            $temoignages = $paginator->paginate(
                $query,
                $request->query->getInt('page', 1),
                9
            );
            return $this->render('/pages/temoignages.html.twig',[
                'temoignages' => $temoignages,
                'form' => $form->createView()
            ]);
        }

        return $this->render('/pages/temoignages.html.twig',[
            'temoignages' => $temoignages,
            'form' => $form->createView()
        ]);
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
        $notifs = $repo->findBy(
            ['receiver' => $this->getUser()],
            ['date' => 'desc' ],
            4,
            null
        );
        $nbNonLu = $repo->findBy([
            'receiver' => $this->getUser(),
            'checked' => 0
        ]);
        return $this->render('partials/request/notifs.html.twig',
            ['notifs' => $notifs, 'notifNonLu' => count($nbNonLu)]
        );
    }

    public function getMessagesUnread(MessageRepository $repo){
        $convs = $this->getUser()->getConversations();
        $count = 0;
        foreach($convs as $conv){
            foreach($conv->getMessages() as $message){
                if ($message->getAuthor() != $this->getUser() && !$message->getIsRead()){
                    $count++;
                }
            }
        }
        return $this->render('partials/request/unreadMessages.html.twig',
            ['messagesCount' => $count]
        );
    }
}
