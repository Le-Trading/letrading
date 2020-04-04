<?php
/**
 * Created by PhpStorm.
 * User: anthonypiquard
 * Date: 2020-03-04
 * Time: 14:59
 */

namespace App\Service;

use App\Repository\EtapeFormationRepository;
use App\Entity\User;
use App\Repository\FollowRepository;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Repository\RepositoryFactory;
use Symfony\Component\Security\Core\Security;

class RequestService {
    private $manager;
    private $followRepo;
    private $security;

    public function __construct(
        EntityManagerInterface $manager,
        FollowRepository $followRepo,
        Security $security,
        EtapeFormationRepository $etapeFormationRepository
    ) {
        $this->manager = $manager;
        $this->security = $security;
        $this->followRepo = $followRepo;
        $this->etapeFormationRepo = $etapeFormationRepository;
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

    public function sortConversationsByLastMessage(User $user){
        $convs = $user->getConversations();
        $lastMessages = [];
        foreach($convs as $conv){
            $lastMessages[$conv->getId()] = $conv->getMessages()->last()->getCreatedAt();
        }
        dd($lastMessages);
        print_r(usort($lastMessages, array($this, "compareByTimeStamp")));
        exit();
    }
    function compareByTimeStamp($time1, $time2)
    {
        if (strtotime($time1->format('Y-m-d H:i:s')) < strtotime($time2->format('Y-m-d H:i:s')))
            return 1;
        else if (strtotime($time1->format('Y-m-d H:i:s')) > strtotime($time2->format('Y-m-d H:i:s')))
            return -1;
        else
            return 0;
    }

    /**
     * Fonction qui permet de récuperer la position pour la création d'une etape de formation
     *
     * @param $idSection
     * @return int
     */
    public function getPositionEtapeFormation($idSection){
        $lastEtape = $this->etapeFormationRepo->findOneBy(
            ['section' => $idSection],
            ['position' => 'desc']
        );

        if(is_null($lastEtape))
            return 0;
        else
            return $lastEtape->getPosition();
    }

    public function countItemsInRepository($repository){
        $count = $repository->createQueryBuilder('t')
            ->select('count(t.id)')
            ->getQuery()
            ->getSingleScalarResult();
        return $count;
    }
}
