<?php

namespace App\Controller;

use App\Entity\History;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\ORM\Query;

class ApiController
{
    /**
     * @Route("/api/exchange/values", name="exchangeValues", methods={"POST"})
     */
    public function exchangeValues(Request $request, EntityManagerInterface $entityManager, ValidatorInterface $validator): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $history = new History();
            $history->setFirstIn($data['first']);
            $history->setSecondIn($data['second']);
            /* // Validate
            $violations = $validator->validate($history);
            if (count($violations) > 0) {
                $errors = [];
                foreach ($violations as $violation) {
                    $errors[] = $violation->getMessage();
                }
    
                return new JsonResponse(['error' => 'Validation failed', 'details' => $errors], 400);
            } */
            $history->setCreationDate(new \DateTime());
            //Save
            $entityManager->persist($history);
            $entityManager->flush();
            
            $savedId = $history->getId();
            //Update
            $this->updateHistory($savedId, $data, $entityManager);

            return new JsonResponse(['message' => 'Values exchanged and saved'], 200);
        }  catch (\TypeError $e) {
            return new JsonResponse(['error' => 'Type error: ' . $e->getMessage()], 400);
        } catch (\Exception $e) {
            return new JsonResponse(['error' => 'Failed to exchanged and saved : ' . $e->getMessage()], 500);
        }
    }

    private function updateHistory(int $savedId, array $data, EntityManagerInterface $entityManager): void
    {
        // Fetch the entity from the database using its ID
        $history = $entityManager->getRepository(History::class)->find($savedId);

        if (!$history) {
            // Handle the case where the entity with the specified ID is not found
            throw new \InvalidArgumentException('History entity not found for ID: ' . $savedId);
        }
        
        // Update specific fields
        $history->setFirstOut($data['second']);
        $history->setSecondOut($data['first']);
        $history->setUpdateDate(new \DateTime());

        // Persist the updated entity
        $entityManager->persist($history);

        // Flush changes to the database
        $entityManager->flush();
    }

    /**
     * @Route("/api/get/history", name="getHistory", methods={"GET"})
     */
    public function getHistory(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer): JsonResponse
    {
        // Retrieve all records from the History table
        $historyRepository = $entityManager->getRepository(History::class);
        $history = $historyRepository->findAll();
        $jsonContent = $serializer->serialize($history, 'json');
        return new JsonResponse($jsonContent, 200, [], true);
    }

    /**
     * @Route("/api/get/history-with-pagination", name="getHistoryWithPagination", methods={"GET"})
     */
    public function getHistoryWithPagination(Request $request, EntityManagerInterface $entityManager, SerializerInterface $serializer, ValidatorInterface $validator): JsonResponse
    {
        // Retrieve all records from the History table with pagination and sorting

        $page = $request->query->get('page', 1);
        $limit = $request->query->get('limit', 10);
        $sortBy = $request->query->get('sortBy', 'id');
        $sortOrder = $request->query->get('sortOrder', 'asc');
        // Validate page, limit, sortBy, and sortOrder
        $errors = $this->validateParameters($page, $limit, $sortBy, $sortOrder, $validator, $entityManager);

        if (!empty($errors)) {
            return new JsonResponse(['error' => $errors], 400);
        }

        $historyRepository = $entityManager->getRepository(History::class);

        // Retrieve paginated and sorted records using a Query instance
        $query = $historyRepository->createQueryBuilder('h')
            ->orderBy('h.' . $sortBy, $sortOrder)
            ->getQuery();
        $paginator = new Paginator($query);
        $paginator->getQuery()
            ->setFirstResult(($page - 1) * $limit)
            ->setMaxResults($limit);

        $totalItems = count($paginator);
        $totalPages = ceil($totalItems / $limit);

        // Paginate and serialize the result
        $history = $paginator->getIterator()->getArrayCopy();
        $jsonContent = $serializer->serialize($history, 'json');

        // Return response with additional information
        return new JsonResponse([
            'data' => json_decode($jsonContent, true),
            'page' => $page,
            'totalItems' => $totalItems,
            'totalPages' => $totalPages,
        ], 200);
    }

    private function validateParameters($page, $limit, $sortBy, $sortOrder, ValidatorInterface $validator, EntityManagerInterface $entityManager): array
    {
        $errors = [];

        // Validate page number
        if (!ctype_digit($page) || $page < 1) {
            $errors[] = 'Invalid page number.';
        }

        // Validate limit
        if (!ctype_digit($limit) || $limit < 1) {
            $errors[] = 'Invalid limit.';
        }

        // Validate sortBy
        $historyMetadata = $entityManager->getClassMetadata(History::class);
        if (!$historyMetadata->hasField($sortBy)) {
            $errors[] = 'Invalid sort field.';
        }

        // Validate sortOrder
        $allowedSortOrders = ['asc', 'desc']; // Add your allowed sort orders
        if (!in_array($sortOrder, $allowedSortOrders)) {
            $errors[] = 'Invalid sort order.';
        }

        return $errors;
    }
}
