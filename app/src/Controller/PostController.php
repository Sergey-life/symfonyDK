<?php

namespace App\Controller;

use App\Entity\Post;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class PostController extends AbstractController
{
    #[Route('/post', name: 'app_post')]
    public function index(ManagerRegistry $doctrine): Response
    {
        $entityManager = $doctrine->getManager();

        $post = new Post();
        $post->setTitle('Топ 10 красивих акордів на гітарі');
        $post->setBody('Am7, Em7, G, H7, ,Dm7 Fsus, F#m, G#7, C, Fmaj');
        $post->setImage('test.img');
        $post->setSlug('top-10-beautiful-guitar-chords');
        $post->setTopicId(1);

        $entityManager->persist($post);
        $entityManager->flush();

        return new Response('Пост з id: '.$post->getId().' успішно збережений!');

//        return $this->render('post/index.html.twig', [
//            'postId' => $post->getId(),
//        ]);
    }
}
