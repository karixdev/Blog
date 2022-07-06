<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostFormType;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/admin/post/new', name: 'new_post')]
    #[IsGranted('ROLE_ADMIN')]
    public function new(Request $request, FileUploader $fileUploader, ManagerRegistry $registry): Response
    {
        $post = new Post();
        $postForm = $this->createForm(PostFormType::class, $post);

        $postForm->handleRequest($request);
        if ($postForm->isSubmitted() && $postForm->isValid()) {

            $banner = $postForm->get('banner')->getData();
            if ($bannerFilename = $fileUploader->upload($banner)) {
                $post
                    ->setBannerFilename($bannerFilename)
                    ->setAuthor($this->getUser())
                ;

                $registry->getManager()->persist($post);
                $registry->getManager()->flush();

                $this->redirectToRoute('admin_dashboard');
            }
        }

        return $this->render('post/new.html.twig', [
            'postForm' => $postForm->createView(),
        ]);
    }
}
