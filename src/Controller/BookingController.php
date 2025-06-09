<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use App\Repository\AvailabilitySlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/booking')]
#[IsGranted('ROLE_USER')]
class BookingController extends AbstractController
{
    #[Route('/', name: 'booking_list', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();
        
        // Get bookings based on user role
        if ($profile->getType() === 'tutor') {
            $bookings = $entityManager->getRepository(Booking::class)->findBy(
                ['tutor' => $profile->getTutorProfile()],
                ['startTime' => 'DESC']
            );
        } else {
            $bookings = $entityManager->getRepository(Booking::class)->findBy(
                ['student' => $profile->getStudentProfile()],
                ['startTime' => 'DESC']
            );
        }

        return $this->render('booking/index.html.twig', [
            'bookings' => $bookings
        ]);
    }

    #[Route('/new', name: 'booking_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        AvailabilitySlotRepository $availabilitySlotRepository
    ): Response {
        $booking = new Booking();
        $booking->setStudent($this->getUser()->getProfile()->getStudentProfile());
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($booking);
            $entityManager->flush();

            $this->addFlash('success', 'Booking request created successfully.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'booking_show', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function show(Booking $booking): Response
    {
        // Check if user has access to this booking
        $user = $this->getUser();
        $profile = $user->getProfile();
        
        if ($profile->getType() === 'tutor' && $booking->getTutor() !== $profile->getTutorProfile()) {
            throw $this->createAccessDeniedException('You can only view your own bookings.');
        }
        
        if ($profile->getType() === 'student' && $booking->getStudent() !== $profile->getStudentProfile()) {
            throw $this->createAccessDeniedException('You can only view your own bookings.');
        }

        return $this->render('booking/show.html.twig', [
            'booking' => $booking
        ]);
    }

    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {
        $this->denyAccessUnlessGranted('edit', $booking);

        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Booking updated successfully.');

            return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/cancel', name: 'booking_cancel', methods: ['POST'])]
    #[IsGranted('ROLE_USER')]
    public function cancel(Booking $booking, EntityManagerInterface $entityManager): Response
    {
        // Check if user has access to cancel this booking
        $user = $this->getUser();
        $profile = $user->getProfile();
        
        if ($profile->getType() === 'tutor' && $booking->getTutor() !== $profile->getTutorProfile()) {
            throw $this->createAccessDeniedException('You can only cancel your own bookings.');
        }
        
        if ($profile->getType() === 'student' && $booking->getStudent() !== $profile->getStudentProfile()) {
            throw $this->createAccessDeniedException('You can only cancel your own bookings.');
        }

        // Only allow cancellation if the booking hasn't started yet
        if ($booking->getStartTime() > new \DateTime()) {
            $booking->setStatus('cancelled');
            $entityManager->flush();
            $this->addFlash('success', 'Booking cancelled successfully.');
        } else {
            $this->addFlash('error', 'Cannot cancel a booking that has already started.');
        }

        return $this->redirectToRoute('booking_list');
    }

    #[Route('/{id}/accept', name: 'app_booking_accept', methods: ['POST'])]
    #[IsGranted('ROLE_TUTOR')]
    public function accept(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {
        if ($booking->getTutor() !== $this->getUser()->getProfile()->getTutorProfile()) {
            throw $this->createAccessDeniedException('You can only accept bookings for your courses.');
        }

        if ($this->isCsrfTokenValid('accept'.$booking->getId(), $request->request->get('_token'))) {
            $booking->setStatus('accepted');
            $entityManager->flush();

            $this->addFlash('success', 'Booking accepted successfully.');
        }

        return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
    }

    #[Route('/{id}/reject', name: 'app_booking_reject', methods: ['POST'])]
    #[IsGranted('ROLE_TUTOR')]
    public function reject(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {
        if ($booking->getTutor() !== $this->getUser()->getProfile()->getTutorProfile()) {
            throw $this->createAccessDeniedException('You can only reject bookings for your courses.');
        }

        if ($this->isCsrfTokenValid('reject'.$booking->getId(), $request->request->get('_token'))) {
            $booking->setStatus('rejected');
            $entityManager->flush();

            $this->addFlash('success', 'Booking rejected successfully.');
        }

        return $this->redirectToRoute('booking_show', ['id' => $booking->getId()]);
    }
} 