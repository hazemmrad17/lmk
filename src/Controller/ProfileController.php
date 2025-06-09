<?php

namespace App\Controller;

use App\Entity\Profile;
use App\Form\ProfileType;
use App\Repository\ProfileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\User\UserPasswordHasherInterface;

#[Route('/profile')]
class ProfileController extends AbstractController
{
    #[Route('/', name: 'profile', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Profile updated successfully.');
            return $this->redirectToRoute('profile');
        }

        return $this->render('profile/index.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/settings', name: 'account_settings', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_USER')]
    public function settings(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        
        $form = $this->createForm(UserSettingsType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Settings updated successfully.');
            return $this->redirectToRoute('account_settings');
        }

        return $this->render('profile/settings.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/billing', name: 'billing', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function billing(): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();

        return $this->render('profile/billing.html.twig', [
            'profile' => $profile,
        ]);
    }

    #[Route('/', name: 'app_profile_show', methods: ['GET'])]
    public function show(): Response
    {
        return $this->render('profile/show.html.twig', [
            'profile' => $this->getUser()->getProfile(),
        ]);
    }

    #[Route('/edit', name: 'app_profile_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $profile = $this->getUser()->getProfile();
        $form = $this->createForm(ProfileType::class, $profile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Your profile has been updated successfully.');

            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('profile/edit.html.twig', [
            'profile' => $profile,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/tutor', name: 'app_profile_tutor', methods: ['GET'])]
    #[IsGranted('ROLE_TUTOR')]
    public function tutorProfile(): Response
    {
        $profile = $this->getUser()->getProfile();
        
        if (!$profile->getTutorProfile()) {
            throw $this->createNotFoundException('Tutor profile not found.');
        }

        return $this->render('profile/tutor.html.twig', [
            'profile' => $profile,
            'tutorProfile' => $profile->getTutorProfile(),
        ]);
    }

    #[Route('/student', name: 'app_profile_student', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function studentProfile(): Response
    {
        $profile = $this->getUser()->getProfile();
        
        if (!$profile->getStudentProfile()) {
            throw $this->createNotFoundException('Student profile not found.');
        }

        return $this->render('profile/student.html.twig', [
            'profile' => $profile,
            'studentProfile' => $profile->getStudentProfile(),
        ]);
    }

    #[Route('/change-password', name: 'app_profile_change_password', methods: ['GET', 'POST'])]
    public function changePassword(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $entityManager->flush();

            $this->addFlash('success', 'Your password has been changed successfully.');

            return $this->redirectToRoute('app_profile_show');
        }

        return $this->render('profile/change_password.html.twig', [
            'form' => $form->createView(),
        ]);
    }
} 