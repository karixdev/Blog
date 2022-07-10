<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class LikeController extends AbstractController
{
    #[Route('/like/post/{id}', name: 'like_post', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function likePost(Request $request, Post $post, EntityManagerInterface $entityManager): Response
    {
        if (!$csrfToken = $request->request->get('like_post_token')) {
            throw new InvalidArgumentException();
        }
        if (!$this->isCsrfTokenValid('post-like', $csrfToken)) {
            throw new InvalidCsrfTokenException();
        }

        if ($post->getLikes()->contains($this->getUser())) {
            $post->removeLike($this->getUser());
        } else {
            $post->addLike($this->getUser());
        }
        $entityManager->flush();

        return $this->redirectToRoute('show_post', [
            'id' => $post->getId(),
        ]);
    }
}
