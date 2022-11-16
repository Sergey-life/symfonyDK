<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Post;
use App\Form\PostType;
use App\Repository\CategoryRepository;
use App\Repository\PostRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
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
//        $entityManager = $doctrine->getManager();
//
//        $post = new Post();
//        $post->setTitle('Топ 10 красивих акордів на гітарі');
//        $post->setBody('Am7, Em7, G, H7, ,Dm7 Fsus, F#m, G#7, C, Fmaj');
//        $post->setImage('test.img');
//        $post->setSlug('top-10-beautiful-guitar-chords');
//        $post->setTopicId(1);
//
//        $entityManager->persist($post);
//        $entityManager->flush();
//
//        return new Response('Пост з id: '.$post->getId().' успішно збережений!');


        return $this->render('post/index.html.twig', [
            'posts' => $postRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'new_post')]
    public function new(Request $request, PostRepository $postRepository, CategoryRepository $categoryRepository,SluggerInterface $slugger): Response
    {
        $post = new Post();
        $form = $this->createForm(PostType::class, $post);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $postImage = $form->get('image')->getData();
            if ($postImage) {
                $originalFilename = pathinfo($postImage->getClientOriginalName(), PATHINFO_FILENAME);
                $safeImageName = $slugger->slug($originalFilename);
                $newFileName = $safeImageName.'-'.uniqid().'.'.$postImage->guessExtension();
                try {
                    $postImage->move(
                        $this->getParameter('images_directory'),
                        $newFileName
                    );
                } catch (FileException $e) {
                    throw $this->createNotFoundException(
                        'При завантаженні зображення сталася помилка!'.$e
                    );
                }
                $post->setImage($newFileName);
                $category = $categoryRepository->find($form->get('category_id')->getData());
                $post->setCategory($category);
            }
            $postRepository->save($post, true);

            return $this->redirectToRoute('all_post', [], Response::HTTP_SEE_OTHER);
        }

        return $this->renderForm('post/new.html.twig', [
            'form' => $form
        ]);
    }

    #[Route('post/{id}', name: 'post_show')]
    // 1-й варіант отримати об'єкт по id
//    public function show(ManagerRegistry $doctrine, int $id)
//    {
//        $post = $doctrine->getRepository(Post::class)->find($id);
//        if (!$post) {
//            throw $this->createNotFoundException(
//                'Продукта з id '.$id.' не знайдено!'
//            );
//        }
//
//        return $this->render('post/show.html.twig', [
//            'post' => $post
//        ]);
//    }

    // 2-й варіант отримати об'єкт по id
    public function show(int $id, PostRepository $postRepository): Response
    {
        $post = $postRepository->find($id);

        return $this->render('post/show.html.twig', [
            'post' => $post
        ]);
    }

    #[Route('/post/edit/{id}', name: 'post_edit')]
    public function update(ManagerRegistry $doctrine, int $id): Response
    {
        $entityManager = $doctrine->getManager();
        $post = $entityManager->getRepository(Post::class)->find($id);

        if (!$post) {
            throw $this->createNotFoundException(
                'Продукта з id '.$id.' не знайдено!'
            );
        }

        $post->setTitle('Топ 10 красивих акордів на фортепіано');
        $entityManager->flush();

        return $this->redirectToRoute('post_show', [
            'id' => $post->getId(),
        ]);
    }
}
