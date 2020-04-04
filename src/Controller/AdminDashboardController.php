<?php

namespace App\Controller;

use App\Client\StripeClient;
use App\Repository\PaymentRepository;
use App\Repository\PostRepository;
use App\Repository\TemoignageRepository;
use App\Repository\ThreadRepository;
use App\Repository\UserRepository;
use App\Service\RequestService;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\NoResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminDashboardController extends AbstractController
{
    /**
     * @Route("/admin/dashboard", name="admin_dashboard")
     * @param TemoignageRepository $temRepo
     * @param UserRepository $userRepo
     * @param PaymentRepository $paymentRepository
     * @param PostRepository $postRepo
     * @param StripeClient $stripe
     * @param RequestService $req
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function index(TemoignageRepository $temRepo, UserRepository $userRepo, PaymentRepository $paymentRepository, PostRepository $postRepo, StripeClient $stripe, RequestService $req)
    {
        $countTemoignages = $req->countItemsInRepository($temRepo);
        $countUser = $req->countItemsInRepository($userRepo);
        $countPosts = $req->countItemsInRepository($postRepo);
//        $countPayments = count($stripe->findAllCharges());
        $countPayments = $req->countItemsInRepository($paymentRepository);
        return $this->render('admin/home.html.twig', [
            'countTemoignages' => $countTemoignages,
            'countUser' => $countUser,
            'countPosts' => $countPosts,
            'countPayments' => $countPayments
        ]);
    }
}
