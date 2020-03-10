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

    public function __construct(EntityManagerInterface $manager, Security $security) {
        $this->manager = $manager;
        $this->security = $security;
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
