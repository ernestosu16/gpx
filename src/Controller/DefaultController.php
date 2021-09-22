<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends _Controller_
{
    #[Route('/', name: 'welcome')]
    public function welcome(): Response
    {
        return $this->render('homer-theme/landing.twig', []);
    }
}
