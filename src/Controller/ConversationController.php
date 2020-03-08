<?php

namespace App\Controller;

use App\Entity\Conversation;
use App\Entity\Message;
use App\Entity\User;
use App\Form\ConversationType;
use App\Form\MessageType;
use App\Form\MessageWithUsersType;
use App\Repository\ConversationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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
            'messages' => $messages,
            'form' => $form->createView()
        ]);
    }
    /**
     * @Route("/conversation/create/{id}", name="create_conversation")
     * @param User $user
     * @param Request $request
     * @param EntityManagerInterface $em
     * @return Response
     */
    public function createConversation(User $user, Request $request, EntityManagerInterface $em){
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
        }
        // @TODO : Si l'utilisateur a déjà une conv avec l'utilisateur, rediriger vers la conv
        return $this->render('conversation/create-conversation.html.twig', [
            'form' => $form->createView(),
            'destinataire' => $user
        ]);
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

        return $this->redirectToRoute('conversation',
            ['idConversation' => $conversation->getId()]
        );
    }


//    /**
//     * @param Request $request
//     * @param $messageId
//     * @Route("/message/{idMessage}/read", requirements={"idMessage": "\d+"},
//     *      options = { "expose" = true },
//     *      name="read-message")
//     * @Method({"POST"})
//     * @return JsonResponse
//     */
//    public function markMessageAsRead(Request $request, $idMessage)
//    {
//        $message = $this->getDoctrine()->getRepository('AppBundle:UserMessage')
//            ->find($idMessage);
//
//        $message->setIsRead(true);
//        $em = $this->getDoctrine()->getManager();
//        try {
//            $em->persist($message);
//            $em->flush();
//
//        } catch (Exception $e ) {
//            return new JsonResponse(['error' => $e->getMessage()]);
//        }
//
//        return new JsonResponse(['ok' => true]);
//    }
}
