<?php
/**
 * Created by PhpStorm.
 * User: anthonypiquard
 * Date: 2020-03-05
 * Time: 17:34
 */

namespace App\Service;

use App\Entity\Notif;
use App\Entity\Post;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mercure\Update;
use Symfony\Component\Messenger\MessageBusInterface;

class NotifService {

    private $manager;
    private $bus;

    public function __construct(EntityManagerInterface $manager, MessageBusInterface $bus) {
        $this->manager = $manager;
        $this->bus = $bus;
    }

    public function sendNotif(User $sender, User $receiver, Post $post, string $typeNotif, bool $isCheckedNotif){
        $notif = new Notif();
        $notif->setSender($sender)
            ->setReceiver($receiver)
            ->setPost($post)
            ->setType($typeNotif)
            ->setChecked($isCheckedNotif);

        $this->manager->persist($notif);
    }

    public function sendMercureNotif(string $type, string $userFullName, string $date, string $threadName, string $postId, string $receiver){
        $updateContent = json_encode([
            'type' => $type,
            'fullName' => $userFullName,
            'time' => $date,
            'threadName' => $threadName,
            'idPost' => $postId
        ]);
        $update = new Update("http://monsite.com/ping",
            $updateContent,
            ["http://monsite.com/user/{$receiver}"]
        );
        $this->bus->dispatch($update);
    }

}
