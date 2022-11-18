<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use App\Service\FileUploader;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

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
                $category = $categoryRepository->find($form->get('categories')->getData());
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
    public function edit(Request $request, PostRepository $postRepository, Post $post): Response
    {
        $post->setImage(
            new File($this->getParameter('images_directory').'/'.$post->getImage())
        );
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postRepository->save($post, true);

            return $this->redirectToRoute('all_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/edit.html.twig', [
            'form' => $form,
            'post' => $post
        ]);
    }
}
