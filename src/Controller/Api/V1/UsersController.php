<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Dto\V1\Users\GetUserQueryDto;
use App\Entity\User;
use App\Filters\V1\UserFilter;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/users', name: 'api_v1_users_')]
class UsersController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'], format: 'json', stateless: true)]
    public function index(#[MapQueryString] GetUserQueryDto $userQueryDto, UserRepository $repository, UserFilter $filter): JsonResponse
    {
        $users = $filter->set($userQueryDto, $repository->createQueryBuilder('u'))
            ->filter()
            ->result();

//        $users = $repository->findAll();

        return $this->json($users, status: Response::HTTP_OK, context: ['groups' => 'user:read']);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], format: 'json', stateless: true)]
    public function show(User $user): JsonResponse
    {
        return $this->json($user, status: Response::HTTP_OK, context: ['groups' => 'user:read']);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(): JsonResponse
    {
        return $this->json(['users' => []], Response::HTTP_CREATED);
    }

    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(): JsonResponse
    {
        return $this->json(['users' => []], Response::HTTP_OK);
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(): JsonResponse
    {
        return $this->json(null, Response::HTTP_NO_CONTENT);
    }
}
