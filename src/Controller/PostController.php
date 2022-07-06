<?php

namespace App\Controller;

use App\Entity\Post;
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
use Symfony\Component\Security\Core\Exception\InvalidCsrfTokenException;

class PostController extends AbstractController
{
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
                    ->setCreatedAt(new DateTime())
                    ->setUpdatedAt(new DateTime())
                ;

                $registry->getManager()->persist($post);
                $registry->getManager()->flush();

                return $this->redirectToRoute('admin_dashboard');
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

        if ($redirectTo = $request->request->get('redirect_to')) {
            $this->redirect($redirectTo);
        }

        return $this->redirectToRoute('admin_dashboard');
    }
}
