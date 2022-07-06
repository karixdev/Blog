<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostController extends AbstractController
{
    #[Route('/admin/post/new', name: 'new_post')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, SluggerInterface $slugger, ManagerRegistry $managerRegistry): Response
    {
        $post = new Post();
        $loginForm = $this->createForm(PostFormType::class, $post);

        return $this->render('post/new.html.twig', [
            'loginForm' => $loginForm->createView(),
        ]);
    }
}
