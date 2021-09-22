<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends _Controller_
{
    #[Route('/', name: 'app_home')]
    public function home(): Response
    {
        return $this->render('homer-theme/landing.twig', []);
    }
}
