<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/search', name: 'app_search')]
    public function search(Request $request, PostRepository $postRepository): Response
    {
        $posts = null;
        if ($phrase = $request->query->get('phrase')) {
            $posts = $postRepository->searchByPhrase($phrase);
        }

        return $this->render('main/search.html.twig', [
            'phrase' => $request->query->get('phrase'),
            'posts' => $posts,
        ]);
    }
}
