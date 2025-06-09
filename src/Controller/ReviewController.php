<?php

namespace App\Controller;

use App\Entity\Review;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/review')]
#[IsGranted('ROLE_USER')]
class ReviewController extends AbstractController
{
    #[Route('/', name: 'app_review_index', methods: ['GET'])]
    public function index(ReviewRepository $reviewRepository): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();

        if ($profile->getTutorProfile()) {
            $reviews = $reviewRepository->findBy(['tutor' => $profile->getTutorProfile()]);
        } else {
            $reviews = $reviewRepository->findBy(['student' => $profile->getStudentProfile()]);
        }

        return $this->render('review/index.html.twig', [
            'reviews' => $reviews,
        ]);
    }

    #[Route('/new', name: 'app_review_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $review = new Review();
        $review->setStudent($this->getUser()->getProfile()->getStudentProfile());
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($review);
            $entityManager->flush();

            $this->addFlash('success', 'Review created successfully.');

            return $this->redirectToRoute('app_review_show', ['id' => $review->getId()]);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(Review $review): Response
    {
        $this->denyAccessUnlessGranted('view', $review);

        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_review_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('edit', $review);

        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Review updated successfully.');

            return $this->redirectToRoute('app_review_show', ['id' => $review->getId()]);
        }

        return $this->render('review/edit.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_review_delete', methods: ['POST'])]
    public function delete(Request $request, Review $review, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('delete', $review);

        if ($this->isCsrfTokenValid('delete'.$review->getId(), $request->request->get('_token'))) {
            $entityManager->remove($review);
            $entityManager->flush();

            $this->addFlash('success', 'Review deleted successfully.');
        }

        return $this->redirectToRoute('app_review_index');
    }
} 