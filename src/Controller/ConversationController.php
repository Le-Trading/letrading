<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\ConversationType;
use App\Form\MessageType;
use App\Form\MessageWithUsersType;
use App\Repository\ConversationRepository;
use App\Service\RequestService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConversationController extends AbstractController
{
    /**
     * Affiche toutes les conversations de l'utilisateur
     * @Route("/conversation", name="user_conversations")
     * @param Request $request
     * @return Response
     */
    public function listeConversations(Request $request)
    {
        $user = $this->getUser();
//        $conversation = new Conversation();
//        $form = $this->createForm(ConversationType::class, $conversation);
        $conversations = $user->getConversations();

        return $this->render('conversation/liste-conversations.html.twig', [
            'conversations' => $conversations,
        ]);
    }

    /**
     * show conversation
     *
     * @Route("/conversation/show/{idConversation}",
     *      requirements={"idConversation" = "\d+"}, name="show_conversation"
     * )
     * @ParamConverter("conversation", options={"mapping": {"idConversation": "id"}})
     * @param Conversation $conversation
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse|Response
     * @throws Exception
     */

    public function detailConversation(Conversation $conversation, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();
        if (!$conversation || !$user) {
            throw new Exception();
        }
        if (!$conversation->hasUser($user)) {
            throw new Exception();
        }

        $messages = $conversation->getMessages();

        $message = new Message();
        $message->setConversation($conversation)
            ->setAuthor($user)
            ->setIsRead(false)
            ->setCreatedAt(new \DateTime("now"));

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $em->persist($message);
            $em->flush();

            return $this->redirectToRoute('show_conversation',
                ['idConversation' => $conversation->getId()]
            );
        }

        return $this->render('conversation/conversation.html.twig', [
            'conversation' => $conversation,
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/conversation/create/{id}", name="create_conversation")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @param RequestService $req
     * @return Response
     */
    public function createConversation(User $user, Request $request, EntityManagerInterface $em, RequestService $req)
    {
        if ($req->hasConversationWith($user)) {
            return $this->redirectToRoute('show_conversation', [
                'idConversation' => $req->hasConversationWith($user)->getId()
            ]);
        } else {
            $currentUser = $this->getUser();
            $conversation = new Conversation();
            $conversation->addParticipants($currentUser)
                ->addParticipants($user);
            $message = new Message();
            $form = $this->createForm(MessageType::class, $message);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                $message = $form->getData();
                $message->setAuthor($currentUser);
                $conversation->addMessage($message);
                $em->persist($conversation);
                $em->persist($message);
                $em->flush();
                return $this->redirectToRoute('show_conversation',
                    ['idConversation' => $conversation->getId()]
                );
            }
            return $this->render('conversation/create-conversation.html.twig', [
                'form' => $form->createView(),
                'destinataire' => $user
            ]);
        }
    }


    /**
     * @Route("/conversation/{idConversation}/save",
     *      requirements={"idConversation": "\d+"}, name="conversation_message_save")
     * @param Conversation $conversation
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return RedirectResponse
     * @throws Exception
     */
    public function saveMessageAction(Conversation $conversation, Request $request, EntityManagerInterface $em)
    {
        $user = $this->getUser();

        $message = new Message();
        $message->setConversation($conversation)
            ->setAuthor($user)
            ->setIsRead(false)
            ->setCreatedAt(new \DateTime("now"));

        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $message = $form->getData();
            $em->persist($message);
            $em->flush();
        }

        return $this->redirectToRoute('show_conversation',
            ['idConversation' => $conversation->getId()]
        );
    }


    /**
     * @param Message $message
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return JsonResponse
     * @Route("/message/{idMessage}/read", requirements={"idMessage": "\d+"},
     *      options = { "expose" = true },
     *      name="read-message")
     * @ParamConverter("message", options={"mapping": {"idMessage": "id"}})
     */
    public function markMessageAsRead(Message $message, Request $request, EntityManagerInterface $em)
    {
        $message->setIsRead(true);
        try {
            $em->persist($message);
            $em->flush();
        } catch (Exception $e ) {
            return new JsonResponse(['error' => $e->getMessage()]);
        }

        return new JsonResponse(['ok' => true]);
    }
}
