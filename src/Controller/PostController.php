<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Post;
use App\Form\CommentFormType;
use App\Form\PostFormType;
use App\Repository\PostRepository;
use App\Service\FileManager;
use DateTime;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class PostController extends AbstractController
{
    #[Route('/post/{id}', name: 'show_post', methods: ['GET'])]
    public function show(Post $post, UrlGeneratorInterface $generator): Response
    {
        $commentForm = $this->createForm(CommentFormType::class, new Comment(), [
            'action' => $generator->generate('new_comment'),
            'postId' => $post->getId(),
        ]);

        return $this->render('post/show.html.twig', [
            'post' => $post,
            'commentForm' => $commentForm->createView(),
        ]);
    }

    #[Route('/admin/post/new', name: 'new_post')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, FileManager $fileManager, ManagerRegistry $registry): Response
    {
        $post = new Post();
        $postForm = $this->createForm(PostFormType::class, $post);

        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {

            $banner = $postForm->get('banner')->getData();
            if ($bannerFilename = $fileManager->upload($banner)) {
                $post
                    ->setBannerFilename($bannerFilename)
                    ->setAuthor($this->getUser())
                ;

                $registry->getManager()->persist($post);
                $registry->getManager()->flush();

                return $this->redirectToRoute('admin_dashboard');
            } else {
                return $this->render('post/new.html.twig', [
                    'postForm' => $postForm->createView(),
                    'bannerError' => true,
                ]);
            }
        }

        return $this->render('post/new.html.twig', [
            'postForm' => $postForm->createView(),
        ]);
    }

    #[Route('/admin/post/delete/{id}', name: 'post_delete', methods: ['POST'])]
    #[IsGranted('POST_DELETE', subject: 'post')]
    public function delete(Request $request, Post $post, FileManager $fileManager, PostRepository $postRepository): Response
    {
        if (!$this->isCsrfTokenValid('post-delete', $request->request->get('token'))) {
            throw new InvalidCsrfTokenException();
        }

        $fileManager->remove($post);
        $postRepository->remove($post, true);

        return $this->redirectToRoute('admin_dashboard');
    }

    #[Route('/admin/post/edit/{id}', name: 'post_edit')]
    #[IsGranted('POST_EDIT', subject: 'post')]
    public function edit(Request $request, Post $post, FileManager $fileManager, ManagerRegistry $registry): Response
    {
        $postForm = $this->createForm(PostFormType::class, $post, [
            'is_image_required' => false,
            'submit_btn_text' => 'save',
        ]);

        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {
            if ($banner = $postForm->get('banner')->getData()) {
                $post->setBannerFilename(
                    $fileManager->replace($banner, $post)
                );
            }

            $registry->getManager()->flush();

            return $this->redirectToRoute('show_post', [
                'id' => $post->getId(),
            ]);
        }

        return $this->render('post/edit.html.twig', [
            'postForm' => $postForm->createView(),
            'post' => $post
        ]);
    }
}
