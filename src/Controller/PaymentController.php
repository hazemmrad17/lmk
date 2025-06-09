<?php

namespace App\Controller;

use App\Entity\Payment;
use App\Form\PaymentType;
use App\Repository\PaymentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/payment')]
#[IsGranted('ROLE_USER')]
class PaymentController extends AbstractController
{
    #[Route('/', name: 'app_payment_index', methods: ['GET'])]
    public function index(PaymentRepository $paymentRepository): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();

        if ($profile->getTutorProfile()) {
            $payments = $paymentRepository->findBy(['booking' => $profile->getTutorProfile()->getBookings()]);
        } else {
            $payments = $paymentRepository->findBy(['booking' => $profile->getStudentProfile()->getBookings()]);
        }

        return $this->render('payment/index.html.twig', [
            'payments' => $payments,
        ]);
    }

    #[Route('/new/{booking_id}', name: 'app_payment_new', methods: ['GET', 'POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function new(
        Request $request,
        int $booking_id,
        EntityManagerInterface $entityManager
    ): Response {
        $payment = new Payment();
        $form = $this->createForm(PaymentType::class, $payment);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $booking = $entityManager->getReference('App\Entity\Booking', $booking_id);
            $payment->setBooking($booking);
            $payment->setStatus('pending');
            $entityManager->persist($payment);
            $entityManager->flush();

            // Here you would typically integrate with a payment gateway like Stripe
            // For now, we'll just mark it as completed
            $payment->setStatus('completed');
            $entityManager->flush();

            $this->addFlash('success', 'Payment processed successfully.');

            return $this->redirectToRoute('app_payment_show', ['id' => $payment->getId()]);
        }

        return $this->render('payment/new.html.twig', [
            'payment' => $payment,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_payment_show', methods: ['GET'])]
    public function show(Payment $payment): Response
    {
        $this->denyAccessUnlessGranted('view', $payment);

        return $this->render('payment/show.html.twig', [
            'payment' => $payment,
        ]);
    }

    #[Route('/create-checkout-session/{booking_id}', name: 'app_payment_create_checkout_session', methods: ['POST'])]
    #[IsGranted('ROLE_STUDENT')]
    public function createCheckoutSession(Request $request, int $booking_id, EntityManagerInterface $entityManager): Response
    {
        $booking = $entityManager->getReference('App\Entity\Booking', $booking_id);
        
        if ($booking->getStudent() !== $this->getUser()->getProfile()->getStudentProfile()) {
            throw $this->createAccessDeniedException('You can only pay for your own bookings.');
        }

        Stripe::setApiKey($this->getParameter('stripe_secret_key'));

        $session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price_data' => [
                    'currency' => 'usd',
                    'product_data' => [
                        'name' => 'Booking Payment',
                    ],
                    'unit_amount' => $booking->getAmount() * 100,
                ],
                'quantity' => 1,
            ]],
            'mode' => 'payment',
            'success_url' => $this->generateUrl('app_payment_success', ['booking_id' => $booking_id], true),
            'cancel_url' => $this->generateUrl('app_payment_cancel', ['booking_id' => $booking_id], true),
        ]);

        return $this->json(['id' => $session->id]);
    }

    #[Route('/success/{booking_id}', name: 'app_payment_success', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function success(int $booking_id, EntityManagerInterface $entityManager): Response
    {
        $booking = $entityManager->getReference('App\Entity\Booking', $booking_id);
        
        if ($booking->getStudent() !== $this->getUser()->getProfile()->getStudentProfile()) {
            throw $this->createAccessDeniedException('You can only view your own payments.');
        }

        $payment = new Payment();
        $payment->setBooking($booking);
        $payment->setAmount($booking->getAmount());
        $payment->setStatus('completed');
        $entityManager->persist($payment);
        $entityManager->flush();

        $this->addFlash('success', 'Payment completed successfully.');

        return $this->redirectToRoute('app_payment_show', ['id' => $payment->getId()]);
    }

    #[Route('/cancel/{booking_id}', name: 'app_payment_cancel', methods: ['GET'])]
    #[IsGranted('ROLE_STUDENT')]
    public function cancel(int $booking_id): Response
    {
        $this->addFlash('error', 'Payment was cancelled.');

        return $this->redirectToRoute('app_booking_show', ['id' => $booking_id]);
    }

    #[Route('/webhook', name: 'app_payment_webhook', methods: ['POST'])]
    public function webhook(Request $request): Response
    {
        $payload = $request->getContent();
        $sig_header = $request->headers->get('Stripe-Signature');
        $endpoint_secret = $this->getParameter('stripe_webhook_secret');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload, $sig_header, $endpoint_secret
            );
        } catch (\UnexpectedValueException $e) {
            return new Response('Invalid payload', Response::HTTP_BAD_REQUEST);
        } catch (\Stripe\Exception\SignatureVerificationException $e) {
            return new Response('Invalid signature', Response::HTTP_BAD_REQUEST);
        }

        if ($event->type === 'checkout.session.completed') {
            $session = $event->data->object;
            // Handle successful payment
        }

        return new Response('Webhook received', Response::HTTP_OK);
    }

    #[Route('/{id}/refund', name: 'app_payment_refund', methods: ['POST'])]
    #[IsGranted('ROLE_TUTOR')]
    public function refund(Request $request, Payment $payment, EntityManagerInterface $entityManager): Response
    {
        if ($payment->getBooking()->getTutor() !== $this->getUser()->getProfile()->getTutorProfile()) {
            throw $this->createAccessDeniedException('You can only refund payments for your bookings.');
        }

        if ($this->isCsrfTokenValid('refund'.$payment->getId(), $request->request->get('_token'))) {
            Stripe::setApiKey($this->getParameter('stripe_secret_key'));

            try {
                $refund = \Stripe\Refund::create([
                    'payment_intent' => $payment->getStripePaymentId(),
                ]);

                $payment->setStatus('refunded');
                $entityManager->flush();

                $this->addFlash('success', 'Payment refunded successfully.');
            } catch (\Exception $e) {
                $this->addFlash('error', 'Failed to refund payment: ' . $e->getMessage());
            }
        }

        return $this->redirectToRoute('app_payment_show', ['id' => $payment->getId()]);
    }
} 