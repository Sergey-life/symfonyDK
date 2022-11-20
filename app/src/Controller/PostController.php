<?php

namespace App\Controller;

use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'all_post')]
    public function index(PostRepository $postRepository): Response
    {
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
            $postImage = $form->get('image')->getData();
            if ($postImage) {
                $postImageName = $fileUploader->upload($postImage);
                $post->setImage($postImageName);
                $category = $categoryRepository->find($form->get('category')->getData());
                $post->setCategory($category);
            }
            $postRepository->save($post, true);

            return $this->redirectToRoute('all_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }

    #[Route('post/{id}', name: 'post_show')]
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
            $postImage = $form->get('image')->getData();
            if ($postImage) {
                $file = $this->getParameter('images_directory').'/'.$post->getImage();
                if (file_exists($file)) {
                    $filesystem->remove($file);
                }
                $postImageName = $fileUploader->upload($postImage);
                $post->setImage($postImageName);
            } else {
                $category = $categoryRepository->find($form->get('category')->getData());
                $post->setCategory($category);
                $postImageOld = $post->getImage();
                $post->setImage($postImageOld);
            }
            $postRepository->save($post, true);

            return $this->redirectToRoute('all_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }
}
