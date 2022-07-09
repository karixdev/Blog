<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\CommentFormType;
use App\Repository\CommentRepository;
use App\Repository\PostRepository;
use DateTime;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class CommentController extends AbstractController
{
    #[Route('/comment/add', name: 'new_comment', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function new(Request $request, PostRepository $postRepository, CommentRepository $commentRepository): Response
    {
        $comment = new Comment();
        $commentForm = $this->createForm(CommentFormType::class, $comment);

        $commentForm->handleRequest($request);
        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment
                ->setPost(
                    $postRepository->find($commentForm->get('post')->getData())
                )
                ->setAuthor($this->getUser())
                ->setCreatedAt(new DateTime())
                ->setUpdatedAt(new DateTime())
            ;
            $commentRepository->add($comment, true);

            return $this->redirectToRoute('show_post', [
                'id' => $comment->getPost()->getId(),
            ]);
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
