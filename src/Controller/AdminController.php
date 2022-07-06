<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[IsGranted('ROLE_ADMIN')]
class AdminController extends AbstractController
{
    #[Route('/admin', name: 'admin_dashboard')]
    public function dashboard(PostRepository $postRepository): Response
    {
        return $this->render('admin/index.html.twig', [
            'posts' => $postRepository->findBy([], [
                'id' => 'DESC',
            ])
        ]);
    }
}
