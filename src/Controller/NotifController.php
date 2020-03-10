<?php

namespace App\Controller;

use App\Repository\NotifRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class NotifController extends AbstractController
{
    /**
     * @Route("/notifications", name="notif_index")
     */
    public function index(NotifRepository $repo, PaginatorInterface $paginator, Request $request)
    {
        $notifs = $paginator->paginate(
            $repo->findBy(
                ['receiver' => $this->getUser()],
                ['date' => 'desc' ]
            ),
            $request->query->getInt('page', 1),
            4
        );
        return $this->render('notif/index.html.twig',[
            'notifs' => $notifs
        ]);
    }
}
