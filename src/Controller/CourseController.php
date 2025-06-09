<?php

namespace App\Controller;

use App\Entity\Course;
use App\Form\CourseType;
use App\Repository\CourseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/course')]
class CourseController extends AbstractController
{
    #[Route('/', name: 'course_list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        // Get all active courses
        $courses = $entityManager->getRepository(Course::class)->findBy(['isActive' => true]);

        return $this->render('course/index.html.twig', [
            'courses' => $courses
        ]);
    }

    #[Route('/new', name: 'course_create', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TUTOR')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $course = new Course();
        $course->setTutor($this->getUser()->getProfile()->getTutorProfile());
        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($course);
            $entityManager->flush();

            $this->addFlash('success', 'Course created successfully.');

            return $this->redirectToRoute('course_show', ['slug' => $course->getSlug()]);
        }

        return $this->render('course/new.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'course_show', methods: ['GET'])]
    public function show(Course $course): Response
    {
        return $this->render('course/show.html.twig', [
            'course' => $course,
        ]);
    }

    #[Route('/{slug}/edit', name: 'course_edit', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_TUTOR')]
    public function edit(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($course->getTutor() !== $this->getUser()->getProfile()->getTutorProfile()) {
            throw $this->createAccessDeniedException('You can only edit your own courses.');
        }

        $form = $this->createForm(CourseType::class, $course);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Course updated successfully.');

            return $this->redirectToRoute('course_show', ['slug' => $course->getSlug()]);
        }

        return $this->render('course/edit.html.twig', [
            'course' => $course,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{slug}', name: 'course_delete', methods: ['POST'])]
    #[IsGranted('ROLE_TUTOR')]
    public function delete(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($course->getTutor() !== $this->getUser()->getProfile()->getTutorProfile()) {
            throw $this->createAccessDeniedException('You can only delete your own courses.');
        }

        if ($this->isCsrfTokenValid('delete'.$course->getId(), $request->request->get('_token'))) {
            $entityManager->remove($course);
            $entityManager->flush();

            $this->addFlash('success', 'Course deleted successfully.');
        }

        return $this->redirectToRoute('course_list');
    }

    #[Route('/{id}/enroll', name: 'app_course_enroll', methods: ['POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function enroll(Request $request, Course $course, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('enroll'.$course->getId(), $request->request->get('_token'))) {
            $studentProfile = $this->getUser()->getProfile()->getStudentProfile();
            $course->addStudent($studentProfile);
            $entityManager->flush();

            $this->addFlash('success', 'You have been enrolled in the course successfully.');
        }

        return $this->redirectToRoute('app_course_show', ['id' => $course->getId()]);
    }
} 