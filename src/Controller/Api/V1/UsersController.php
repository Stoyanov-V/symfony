<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Dto\V1\Users\CreateUserRequestDto;
use App\Dto\V1\Users\GetUserQueryDto;
use App\Entity\User;
use App\Filters\V1\UserFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[Route('/api/v1/users', name: 'api_v1_users_', format: 'json', stateless: true)]
final class UsersController extends AbstractController
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString] GetUserQueryDto $userQueryDto,
        UserRepository $repository,
        UserFilter $filter): JsonResponse
    {
        $users = $filter
            ->set($userQueryDto, $repository->createQueryBuilder('u'))
            ->filter()
            ->sort()
            ->paginate();

        $context = $filter->include($userQueryDto);

        return $this->json($users, status: Response::HTTP_OK, context: $context);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json($user, status: Response::HTTP_OK, context: ['groups' => 'user:read']);
    }

    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateUserRequestDto $dto,
        DenormalizerInterface $denormalizer,
    ): JsonResponse
    {
        $user = $denormalizer->denormalize(
            (array) $dto,
            type: User::class,
            context: ['groups' => 'user:write']
        );

        $this->em->persist($user);
        $this->em->flush();

        return $this->json($user, Response::HTTP_CREATED, context: ['groups' => 'user:read']);
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
