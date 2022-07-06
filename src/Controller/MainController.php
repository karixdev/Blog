<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_index')]
    public function index(PostRepository $postRepository): Response
    {
        return $this->render('main/index.html.twig', [
            'posts' => $postRepository->findBy([], [
                'id' => 'DESC',
            ])
        ]);
    }
}
