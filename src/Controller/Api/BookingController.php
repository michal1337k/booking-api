<?php

namespace App\Controller\Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\Repository\SlotRepository;
use App\Entity\Booking;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;



final class BookingController extends AbstractController
{
    
    #[Route('/api/bookings', methods: ['POST'])]
    public function create(Request $request, SlotRepository $slotRepo, EntityManagerInterface $em): JsonResponse 
    {
        $data = json_decode($request->getContent(), true);

        $slotId = $data['slot_id'] ?? null;

        if (!$slotId) {
            return new JsonResponse(['error' => 'slot_id required'], 400);
        }

        $slot = $slotRepo->find($slotId);

        if (!$slot) {
            return new JsonResponse(['error' => 'Slot not found'], 404);
        }

        
        if ($slot->getBooking() !== null) {
            return new JsonResponse(['error' => 'Slot already booked'], 400);
        }

        $booking = new Booking();
        $booking->setUser($this->getUser());
        $booking->setSlot($slot);
        $booking->setCreatedAt(new \DateTimeImmutable());
        $booking->setStatus("confirmed");

        $em->persist($booking);

        try {
            $em->flush();
        } catch (\Doctrine\DBAL\Exception\UniqueConstraintViolationException $e) {
            return new JsonResponse(['error' => 'Slot already booked'], 400);
        }

        return new JsonResponse([
            'message' => 'Booking created',
            'booking_id' => $booking->getId()
        ], 201);
    }

    #[Route('/api/bookings', methods: ['GET'])]
    public function list(): JsonResponse
    {
        /** @var User $user */
        $user = $this->getUser();

        $data = array_map(fn($booking) => [
            'id' => $booking->getId(),
            'slot' => [
                'startAt' => $booking->getSlot()->getStartAt()->format('Y-m-d H:i'),
                'endAt' => $booking->getSlot()->getEndAt()->format('Y-m-d H:i'),
            ],
            'status' => $booking->getStatus()->value,
        ], $user->getBookings()->toArray());

        return new JsonResponse($data);
    }


    #[Route('/api/bookings/{id}', methods: ['DELETE'])]
    public function delete(Booking $booking, EntityManagerInterface $em): JsonResponse 
    {
        if ($booking->getUser() !== $this->getUser()) {
            return new JsonResponse(['error' => 'Forbidden'], 403);
        }

        $em->remove($booking);
        $em->flush();

        return new JsonResponse(null, 204);
    }

}
