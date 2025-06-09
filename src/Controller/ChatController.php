<?php

namespace App\Controller;

use App\Entity\ChatThread;
use App\Entity\Message;
use App\Form\MessageType;
use App\Repository\ChatThreadRepository;
use App\Repository\MessageRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/chat')]
#[IsGranted('ROLE_USER')]
class ChatController extends AbstractController
{
    #[Route('/', name: 'app_chat_index', methods: ['GET'])]
    public function index(ChatThreadRepository $chatThreadRepository): Response
    {
        $user = $this->getUser();
        $profile = $user->getProfile();

        if ($profile->getTutorProfile()) {
            $threads = $chatThreadRepository->findBy(['tutor' => $profile->getTutorProfile()]);
        } else {
            $threads = $chatThreadRepository->findBy(['student' => $profile->getStudentProfile()]);
        }

        return $this->render('chat/index.html.twig', [
            'threads' => $threads,
        ]);
    }

    #[Route('/thread/{id}', name: 'app_chat_thread', methods: ['GET'])]
    public function thread(ChatThread $thread): Response
    {
        $this->denyAccessUnlessGranted('view', $thread);

        return $this->render('chat/thread.html.twig', [
            'thread' => $thread,
        ]);
    }

    #[Route('/thread/{id}/message', name: 'app_chat_message_new', methods: ['POST'])]
    public function newMessage(Request $request, ChatThread $thread, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('view', $thread);

        $message = new Message();
        $message->setThread($thread);
        $message->setSender($this->getUser()->getProfile());
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($message);
            $entityManager->flush();

            return $this->redirectToRoute('app_chat_thread', ['id' => $thread->getId()]);
        }

        return $this->render('chat/_message_form.html.twig', [
            'message' => $message,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/thread/{id}/messages', name: 'app_chat_messages', methods: ['GET'])]
    public function getMessages(ChatThread $thread): JsonResponse
    {
        $this->denyAccessUnlessGranted('view', $thread);

        $messages = $thread->getMessages()->map(function (Message $message) {
            return [
                'id' => $message->getId(),
                'content' => $message->getContent(),
                'sender' => $message->getSender()->getUser()->getEmail(),
                'createdAt' => $message->getCreatedAt()->format('Y-m-d H:i:s'),
            ];
        })->toArray();

        return $this->json($messages);
    }
} 