<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Entity\PostLike;
use App\Repository\CommentRepository;
use App\Repository\PostLikeRepository;
use DateTime;
use Doctrine\ORM\NonUniqueResultException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use UnexpectedValueException;

class LikeController extends AbstractController
{
    #[Route('/like/post/{id}', name: 'like_post', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function likePost(Request $request, Post $post, PostLikeRepository $postLikeRepository): Response
    {
        return new Response();

        if (!$csrfToken = $request->request->get('like_post_token')) {
            throw new UnexpectedValueException();
        }
        if (!$this->isCsrfTokenValid('post-like-token', $csrfToken)) {
            throw new InvalidCsrfTokenException();
        }

        try {
            if ($postLike = $postLikeRepository->findByPostAndUser($post, $this->getUser())) {
                $postLikeRepository->remove($postLike, true);
            } else {
                $newPostLike = new PostLike();
                $newPostLike
                    ->setUser($this->getUser())
                    ->setPost($post)
                    ->setLikedAt(new DateTime())
                ;
                $postLikeRepository->add($newPostLike, true);
            }
        } catch (NonUniqueResultException $nonUniqueResultException) {
            // TODO: handle this exception
        }

        return $this->redirectToRoute('show_post', [
            'id' => $post->getId(),
        ]);
    }
}
