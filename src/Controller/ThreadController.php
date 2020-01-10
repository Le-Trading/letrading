<?php

namespace App\Controller;

use App\Entity\Post;
use App\Entity\Thread;
use App\Form\PostType;
use App\Service\GrantedService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ThreadController extends AbstractController
{
    /**
     * @Route("/thread/{slug}", name="thread_show")
     */
    public function index(Thread $thread, EntityManagerInterface $manager, Request $request, GrantedService $grantedService)
    {

        $post = new Post();
        $form = $this->createForm(PostType::class, $post, ['isAdmin' => $grantedService->isGranted($this->getUser(), 'ROLE_ADMIN')]);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {

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

        return $this->render('thread/index.html.twig', [
            'thread' => $thread,
            'form' => $form->createView()
        ]);
    }
}
