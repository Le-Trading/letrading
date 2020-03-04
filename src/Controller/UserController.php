<?php

namespace App\Controller;

use App\Entity\Follow;
use App\Entity\User;
use App\Repository\FollowRepository;
use App\Service\RequestService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserController extends AbstractController
{
    /**
     * @Route("/user/{pseudo}", name="user_show")
     */
    public function index(User $user, RequestService $requestService)
    {
        if($user == $this->getUser()){
            return $this->redirectToRoute('account_index');
        }
        $isFollowed = $requestService->isFollowedByUser($user->getId());
        return $this->render('user/index.html.twig', [
            'user' => $user,
            'isFollowed' => $isFollowed
        ]);
    }

    /**
     * Fonction pour suivre un utilisateur
     *
     * @Route("/user/{pseudo}/follow", name="user_follow")
     * @return Response
     */
    public function follow(User $userFollowed, FollowRepository $repo, EntityManagerInterface $manager, RequestService $requestService): Response{
        $user = $this->getUser();
        $isFollowed = $requestService->isFollowedByUser($userFollowed->getId());

        if (!$user || $user == $userFollowed) return $this->json([
            'code' => 403,
            'message' => 'Unauthorized'
        ], 403);

        if ($isFollowed){
            $followUser = $repo->findOneBy([
                'follower' => $user,
                'followed' => $userFollowed
            ]);
            $manager->remove($followUser);

            $manager->flush();
            return $this->json(['code' => 200, 'message' => 'Follower supprime'], 200);
        }

        $followUser = new Follow();
        $followUser->setFollower($user)
            ->setFollowed($userFollowed);

        $manager->persist($followUser);
        $manager->flush();

        return $this->json(['code' => 200, 'message' => 'Nouveau follower'], 200);
    }

}
