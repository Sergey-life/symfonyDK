<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestTestController extends AbstractController
{
    #[Route('/test/test', name: 'app_test_test')]
    public function index(): Response
    {
        return $this->render('test_test/index.html.twig', [
            'controller_name' => 'TestTestController',
        ]);
    }
}
