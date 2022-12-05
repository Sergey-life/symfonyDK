<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'all_post')]
    public function index(PostRepository $postRepository, LoggerInterface $logger): Response
    {
        $logger->info('test controller');
        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new_post')]
    public function new(Request $request, PostRepository $postRepository, CategoryRepository $categoryRepository, FileUploader $fileUploader): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**@var UploadedFile $postImage*/
            if ($postImage = $form->get('image')->getData()) {
                $post->setImage($fileUploader->upload($postImage));
                $post->setCategory($categoryRepository->find($form->get('category')->getData()));
                $post->setCreatedAt(new \DateTime());
            }
            $postRepository->save($post, true);

            return $this->redirectToRoute('all_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('post/{id}', name: 'post_show', methods: ['GET'])]
    public function show(int $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/post/edit/{id}', name: 'post_edit')]
    public function edit(Request $request, PostRepository $postRepository, Post $post, FileUploader $fileUploader, CategoryRepository $categoryRepository): Response
    {
        $filesystem = new Filesystem();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /**@var UploadedFile $postImage*/
            if ($postImage = $form->get('image')->getData()) {
                $file = $this->getParameter('images_directory').'/'.$post->getImage();
                if (file_exists($file)) {
                    $filesystem->remove($file);
                }
                $post->setImage($fileUploader->upload($postImage));
            }
            $post->setCategory($categoryRepository->find($form->get('category')->getData()));
            $post->setUpdatedAt(new \DateTime());
            $postRepository->save($post, true);

            return $this->redirectToRoute('all_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('/{id}', name: 'post_delete', methods: ['POST', 'DELETE'])]
    public function delete(Request $request, Post $post, PostRepository $postRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$post->getId(), $request->request->get('delete'))) {
            $file = $this->getParameter('images_directory').'/'.$post->getImage();
            if (file_exists($file)) {
                $filesystem = new Filesystem();
                $filesystem->remove($file);
            }
            $postRepository->remove($post, true);
        }

        return $this->redirectToRoute('all_post', [], Response::HTTP_SEE_OTHER);
    }
}
