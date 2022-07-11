<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Service\LikeManager;
use InvalidArgumentException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

#[IsGranted('ROLE_USER')]
class LikeController extends AbstractController
{
    #[Route('/like/post/{id}', name: 'like_post', methods: ['POST'])]
    public function likePost(Request $request, Post $post, LikeManager $likeManager): Response
    {
        if (!$csrfToken = $request->request->get('like_post_token')) {
            throw new InvalidArgumentException();
        }
        if (!$this->isCsrfTokenValid('post-like', $csrfToken)) {
            throw new InvalidCsrfTokenException();
        }

        $likeManager->action($post, $this->getUser());

        return $this->redirectToRoute('show_post', [
            'id' => $post->getId(),
        ]);
    }

    #[Route('/like/comment/{id}', name: 'like_comment', methods: ['POST'])]
    public function likeComment(Request $request, Comment $comment, LikeManager $likeManager): Response
    {
        if (!$csrfToken = $request->request->get('like_comment_token')) {
            throw new InvalidArgumentException();
        }
        if (!$this->isCsrfTokenValid('comment-like', $csrfToken)) {
            throw new InvalidCsrfTokenException();
        }

        $likeManager->action($comment, $this->getUser());

        return $this->redirectToRoute('show_post', [
             'id' => $comment->getPost()->getId(),
        ]);
    }
}
