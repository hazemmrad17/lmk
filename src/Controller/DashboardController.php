<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractController
{
    #[Route('/dashboard', name: 'dashboard')]
    public function index(): Response
    {
        // Get the current user
        $user = $this->getUser();
        
        // If no user is logged in, redirect to login
        if (!$user) {
            return $this->redirectToRoute('login');
        }

        // Get the user's profile
        $profile = $user->getProfile();
        
        // Render different dashboard based on user type
        if ($profile->getType() === 'tutor') {
            return $this->render('dashboard/tutor.html.twig', [
                'user' => $user,
                'profile' => $profile
            ]);
        } else {
            return $this->render('dashboard/student.html.twig', [
                'user' => $user,
                'profile' => $profile
            ]);
        }
    }
} 