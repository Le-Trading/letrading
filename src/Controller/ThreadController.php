<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Thread;
use App\Entity\User;
use App\Form\PostType;
use App\Entity\PostVote;
use App\Form\ResponseType;
use App\Service\GrantedService;
use App\Repository\PostRepository;
use App\Repository\ThreadRepository;
use App\Repository\PostVoteRepository;
use App\Service\MercureCookieGenerator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Component\Serializer\SerializerInterface;

class ThreadController extends AbstractController
{
    /**
     * @Route("/thread/{slug}", name="thread_show")
     */
    public function index(Thread $thread, EntityManagerInterface $manager, Request $request, GrantedService $grantedService, PostRepository $repo, MessageBusInterface $bus, SerializerInterface $serializer, MercureCookieGenerator $cookieGenerator)
    {
        $post = new Post();

        /**************** FORMULAIRE POUR UN POST *********************/

        $form = $this->createForm(PostType::class, $post, ['isAdmin' => $grantedService->isGranted($this->getUser(), 'ROLE_ADMIN')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            //verification si contenu existe
            if($post->getContent() == null && $post->getMedia() == null && $post->getFeeling()== null){
                $this->addFlash(
                    'danger',
                    "Votre message ne possède pas de contenu"
                );
            }else{
                $post->setAuthor($this->getUser())
                    ->setThread($thread)
                    ->setContent($post->getContent());
                if ($grantedService->isGranted($this->getUser(), 'ROLE_ADMIN')) {
                    if (empty($post->getIsAdmin())) {
                        $isAdmin = false;
                    } else {
                        $isAdmin = true;
                    }
                    $post->setIsAdmin($isAdmin);
                }
                $manager->persist($post);
                $manager->flush();
                $this->addFlash(
                    'success',
                    "Votre message a bien été enregistré"
                );
                return $this->redirectToRoute('thread_show', ['slug' => $thread->getSlug(), 'withAlert' => true]);
            }
        }

        /**************** FORMULAIRE POUR UNE REPONSE A UN POST *********************/
        $formReply = $this->createForm(ResponseType::class, $post, ['isResponse' => true]);
        $formReply->handleRequest($request);

        if ($formReply->isSubmitted() && $formReply->isValid()) {
            $idRespond = $formReply->get('respond')->getData();
            $post->setAuthor($this->getUser())
                ->setThread($thread)
                ->setContent($post->getContent());
            $post->setRespond($repo->find($idRespond));
            $manager->persist($post);
            $manager->flush();
            $this->addFlash(
                'success',
                "Votre message a bien été enregistré"
            );

            //envoi notif mercure
            $updateContent = json_encode([
                'type' => 'comment',
                'fullName' => $post->getAuthor()->getFullName(),
                'time' => $post->getCreatedAt(),
                'threadName' => $post->getThread()->getSlug(),
                'idPost' => $post->getId()
            ]);
            $update = new Update("http://monsite.com/ping",
                $updateContent,
                ["http://monsite.com/user/{$post->getRespond()->getAuthor()->getId()}"]
            );
            $bus->dispatch($update);

            return $this->redirectToRoute('thread_show', ['slug' => $thread->getSlug(), 'withAlert' => true]);
        }
         $response = $this->render('thread/index.html.twig', [
            'thread' => $thread,
            'form' => $form->createView(),
            'formReply' => $formReply->createView(),
        ]);
        $response->headers->set('set-cookie', $cookieGenerator->generate($this->getUser()));
        return $response;
    }

    /**
     * Permet de supprimer un post
     *
     * @Route("/thread/{slug}/post/{id}/delete", name="thread_post_delete")
     * @ParamConverter("thread", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("post", options={"mapping": {"id": "id"}})
     * 
     * @param Thread $thread
     * @param Post $post
     * @param EntityManagerInterface $manager
     * @return void
     */
    public function delete(Thread $thread, Post $post, EntityManagerInterface $manager, GrantedService $grantedService)
    {
        if ($grantedService->isGranted($this->getUser(), 'ROLE_ADMIN')) {
            $manager->remove($post);
            $manager->flush();
            $this->addFlash(
                'success',
                "Le post a bien été supprimé"
            );
        } else {
            $this->addFlash(
                'danger',
                "Vous n'avez pas les droits pour supprimer ce post."
            );
        }
        return $this->redirectToRoute("thread_show", ['slug' => $thread->getSlug()]);
    }

    /**
     * Permet de liker ou de delike un post
     * @Route("/thread/{slug}/post/{id}/like", name="thread_post_like")
     * @ParamConverter("thread", options={"mapping": {"slug": "slug"}})
     * @ParamConverter("post", options={"mapping": {"id": "id"}})
     * 
     * @param Post $post
     * @param ObjectManager $manager
     * @param PostVoteRepository $voteRepo
     * @return Response
     */
    public function vote(Post $post, EntityManagerInterface $manager, PostVoteRepository $voteRepo, MessageBusInterface $bus, SerializerInterface $serializer): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ], 403);

        if ($post->isLikedByUser($user)) {
            $vote = $voteRepo->findOneBy([
                'post' => $post,
                'user' => $user
            ]);
            $manager->remove($vote);
            $manager->flush();
            return $this->json(['code' => 200, 'message' => 'Like supprimé', 'votes' => $voteRepo->count(['post' => $post])], 200);
        }
        $vote = new PostVote();
        $vote->setPost($post)
            ->setUser($user);
        $manager->persist($vote);
        $manager->flush();

        //envoi notif mercure
        $updateContent = json_encode([
            'type' => 'like',
            'fullName' => $user->getFullName(),
            'time' => 'Maintenant',
            'threadName' => $post->getThread()->getSlug(),
            'idPost' => $post->getId()
        ]);
        $update = new Update("http://monsite.com/ping",
            $updateContent,
            ["http://monsite.com/user/{$post->getAuthor()->getId()}"]
        );
        $bus->dispatch($update);

        return $this->json(['code' => 200, 'message' => 'Liké', 'votes' => $voteRepo->count(['post' => $post])], 200);
    }
}
