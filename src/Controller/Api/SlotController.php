<?php

namespace App\Controller\Api;

use App\Entity\Slot;
use App\Repository\SlotRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

final class SlotController extends AbstractController
{
    #[Route('/api/slots', methods: ['GET'])]
    public function slots(SlotRepository $repo): JsonResponse
    {
        $slots = $repo->findAll();

        $data = array_map(fn($slot) => [
            'id' => $slot->getId(),
            'startAt' => $slot->getStartAt()->format('Y-m-d H:i'),
            'endAt' => $slot->getEndAt()->format('Y-m-d H:i'),
            'isBooked' => $slot->getBooking() !== null,
        ], $slots);

        return new JsonResponse($data);
    }

    #[Route('/api/slots', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = json_decode($request->getContent(), true);

        $startAt = new \DateTime($data['startAt'] ?? '');
        $endAt = new \DateTime($data['endAt'] ?? '');

        $slot = new Slot();
        $slot->setStartAt($startAt)
             ->setEndAt($endAt);
        $em->persist($slot);
        $em->flush();

        return new JsonResponse([
            'id' => $slot->getId(),
            'startAt' => $slot->getStartAt()->format('Y-m-d H:i'),
            'endAt' => $slot->getEndAt()->format('Y-m-d H:i')
        ], 201);
    }

}
