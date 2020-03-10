<?php
/**
 * Created by PhpStorm.
 * User: anthonypiquard
 * Date: 2020-03-04
 * Time: 14:59
 */

namespace App\Service;

use App\Entity\User;
use App\Repository\FollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class RequestService {
    private $manager;
    private $followRepo;
    private $security;

    public function __construct(EntityManagerInterface $manager, FollowRepository $followRepo, Security $security) {
        $this->manager = $manager;
        $this->security = $security;
        $this->followRepo = $followRepo;
    }

    /**
     * Fonction pour récuperer tout les abonnés de l'utilisateur connecté
     *
     * @return \App\Entity\Follow[]
     */
    public function getFollowers(){
        $userActive = $this->security->getUser()->getId();
        $followers = [];
        $arrayFollowers = $this->followRepo->findBy(
            ['followed' => $userActive]
        );
        foreach ($arrayFollowers as $follower){
            array_push($followers,$follower->getFollower()->getId());
        }
        return $followers;
    }

    /**
     * Fonction pour savoir si l'utilisateur connecté est abonné a l'utilisateur sur la page
     *
     * @param $userFollowed
     * @return bool
     */
    public function isFollowedByUser($userFollowed){
        $userActive = $this->security->getUser()->getId();
        $followers = $this->followRepo->findBy(
            [
                'followed' => $userFollowed,
                'follower' => $userActive
            ]
        );
        if(empty($followers)){
            return false;
        }else{
            return true;
        }
    }

    public function hasConversationWith(User $user){
        $userActive = $this->security->getUser();
        foreach($userActive->getConversations() as $conversation){
            foreach($conversation->getParticipants() as $participant){
                if ($user == $participant){
                    return $conversation;
                }
            }
        }
        return false;
    }
}
