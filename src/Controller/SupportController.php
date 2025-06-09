<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    #[Route('/support', name: 'support')]
    public function support(): Response
    {
        return $this->render('support/index.html.twig', [
            'title' => 'Support',
        ]);
    }

    #[Route('/documentation', name: 'documentation')]
    public function documentation(): Response
    {
        return $this->render('support/documentation.html.twig', [
            'title' => 'Documentation',
        ]);
    }
} 