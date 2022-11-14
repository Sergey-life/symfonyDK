<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController
{
    #[Route('/', 'home')]
    public function index(Request $request): Response
    {
        if ($name = $request->query->get('hello')) {
            $resp = sprintf('<h1>Привіт %s!</h1>', htmlspecialchars($name));
        }
        else
        {
            $resp = 'Привіт';
        }

        return new Response($resp);
    }
}