<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Thread;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PostController extends AbstractController
{
    /**
     * @Route("/thread/{slug}/post/{id}", name="post_show")
     */
    public function index(Thread $thread, Post $post)
    {
        return $this->render('post/index.html.twig', [
            'controller_name' => 'PostController',
        ]);
    }
}
