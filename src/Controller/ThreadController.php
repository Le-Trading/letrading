<?php

namespace App\Controller;

use App\Entity\Notif;
use App\Entity\Post;
use App\Entity\Thread;
use App\Entity\User;
use App\Form\PostType;
use App\Entity\PostVote;
use App\Form\ResponseType;
use App\Repository\NotifRepository;
use App\Repository\UserRepository;
use App\Service\GrantedService;
use App\Repository\PostRepository;
use App\Repository\ThreadRepository;
use App\Repository\PostVoteRepository;
use App\Service\MailingService;
use App\Service\MercureCookieGenerator;
use App\Service\NotifService;
use App\Service\RequestService;
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
    public function index(
        Thread $thread,
        EntityManagerInterface $manager,
        Request $request,
        GrantedService $grantedService,
        RequestService $requestService,
        PostRepository $repo,
        UserRepository $repoUser,
        MessageBusInterface $bus,
        MercureCookieGenerator $cookieGenerator,
        MailingService $mailingService,
        NotifService $notifService
    )
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

                //envoi notif a tt le monde si message admin
                if($post->getIsAdmin() == true){
                    $forumUsers = $repoUser->findAll();
                    foreach ($forumUsers as $forumUser) {
                        $notifService->sendNotif(
                            $this->getUser(),
                            $forumUser,
                            $post,
                            'admin',
                            0
                        );
                    }
                }else{
                    //envoi notif aux abonnés
                    $followers = $requestService->getFollowers();
                    foreach ($followers as $follower){
                        $notifService->sendNotif(
                            $this->getUser(),
                            $repoUser->find($follower),
                            $post,
                            'comment',
                            0
                        );

                        //envoi notif mercure
                        $notifService->sendMercureNotif(
                            'comment',
                            $post->getAuthor()->getFullName(),
                            date("Y-m-d H:i:s"),
                            $post->getThread()->getSlug(),
                            '',
                            $follower
                        );
                    }

                    //check si mention @user
                    $notifContent = explode("mentionId", $post->getContent());
                    foreach($notifContent as $notifIdUser) {
                        if(is_numeric($notifIdUser)){
                            //envoi notif
                            $notifService->sendNotif(
                                $this->getUser(),
                                $repoUser->find($notifIdUser),
                                $post,
                                'comment',
                                0
                            );

                            //envoi mail
                            $mailingService->notifSend($this->getUser(),$repoUser->find($notifIdUser));

                            //envoi notif mercure
                            $notifService->sendMercureNotif(
                                'comment',
                                $post->getAuthor()->getFullName(),
                                date("Y-m-d H:i:s"),
                                $post->getThread()->getSlug(),
                                '',
                                $notifIdUser
                            );
                        }
                    }
                }

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

            //envoi notif bdd
            $notifService->sendNotif(
                $this->getUser(),
                $post->getRespond()->getAuthor(),
                $repo->find($idRespond),
                'comment',
                0
            );

            $manager->flush();
            $this->addFlash(
                'success',
                "Votre message a bien été enregistré"
            );

            //envoi notif mercure
            $notifService->sendMercureNotif(
                'comment',
                $post->getAuthor()->getFullName(),
                date("Y-m-d H:i:s"),
                $post->getThread()->getSlug(),
                $post->getRespond()->getId(),
                $post->getRespond()->getAuthor()->getId()
            );

            return $this->redirectToRoute('thread_show', ['slug' => $thread->getSlug(), 'withAlert' => true]);
        }

        $users = $manager->createQuery(
            'SELECT u.id, u.pseudo, u.firstName, u.lastName 
                    FROM App\Entity\User u
                    ')
            ->getScalarResult();

         $response = $this->render('thread/index.html.twig', [
            'thread' => $thread,
             'users' => $users,
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
    public function vote(
        Post $post,
        EntityManagerInterface $manager,
        PostVoteRepository $voteRepo,
        NotifRepository $notifRepo,
        NotifService $notifService
    ): Response
    {
        $user = $this->getUser();
        if (!$user) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ], 403);

        if ($post->isLikedByUser($user)){
            $vote = $voteRepo->findOneBy([
                'post' => $post,
                'user' => $user
            ]);
            $manager->remove($vote);

            //suppression notif
            $notif = $notifRepo->findOneBy([
                'sender' => $user,
                'receiver' => $post->getAuthor(),
                'type' => 'like',
                'post' => $post
            ]);
            $manager->remove($notif);

            $manager->flush();
            return $this->json(['code' => 200, 'message' => 'Like supprimé', 'votes' => $voteRepo->count(['post' => $post])], 200);
        }
        $vote = new PostVote();
        $vote->setPost($post)
            ->setUser($user);
        $manager->persist($vote);

        //envoi notif bdd
        $notifService->sendNotif(
            $user,
            $post->getAuthor(),
            $post,
            'like',
            0
        );
        $manager->flush();

        //envoi notif mercure
        $notifService->sendMercureNotif(
            'like',
            $user->getFullname(),
            'Maintenant',
            $post->getThread()->getSlug(),
            $post->getId(),
            $post->getAuthor()->getId()
        );

        return $this->json(['code' => 200, 'message' => 'Liké', 'votes' => $voteRepo->count(['post' => $post])], 200);
    }
}
