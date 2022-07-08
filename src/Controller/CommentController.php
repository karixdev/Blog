<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;
use UnexpectedValueException;

class CommentController extends AbstractController
{
    #[Route('/comment/add', name: 'new_comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PostRepository $postRepository, CommentRepository $commentRepository): Response
    {
        if (!$this->isCsrfTokenValid('new-comment', $request->request->get('comment_token'))) {
            throw new InvalidCsrfTokenException();
        }

        if (!$postId = $request->request->get('post_id')) {
            throw new UnexpectedValueException(
                'Comment is missing a post'
            );
        }

        if (!$post = $postRepository->find($postId)) {
            throw $this->createNotFoundException(
                'Post has not been found'
            );
        }

        $content = $request->request->get('content');
        if (!$content) {
            throw new UnexpectedValueException(
                'Comment is missing a content'
            );
        }

        $comment = new Comment();
        $comment
            ->setContent($content)
            ->setAuthor($this->getUser())
            ->setPost($post)
            ->setCreatedAt(new DateTime())
            ->setUpdatedAt(new DateTime())
        ;
        $commentRepository->add($comment, true);

        if ($redirectTo = $request->request->get('redirect_to')) {
            return $this->redirect($redirectTo);
        }

        return $this->redirectToRoute('app_index');
    }

    #[Route('/comment/delete/{id}', name: 'comment_delete', methods: ['POST'])]
    #[IsGranted('COMMENT_DELETE', subject: 'comment')]
    public function delete(Request $request, Comment $comment, CommentRepository $commentRepository): Response
    {
        if (!$this->isCsrfTokenValid('delete-comment', $request->request->get('token'))) {
            throw new InvalidCsrfTokenException();
        }

        $postId = $comment->getPost()->getId();
        $commentRepository->remove($comment, true);

        return $this->redirectToRoute('show_post', [
            'id' => $postId,
        ]);
    }
}
