<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Attribute\MapUpdateRequestPayload;
use App\Service\Mappers\UserMapper;
use App\Dto\V1\Users\{GetUserQueryDto, CreateUserRequestDto, UpdateUserRequestDto};
use App\Entity\User;
use App\Filters\V1\UserFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\HttpKernel\Attribute\{MapQueryString, MapRequestPayload};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route('/api/v1/users', name: 'api_v1_users_', format: 'json', stateless: true)]
final class UsersController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UserMapper $mapper,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString] GetUserQueryDto $userQueryDto,
        UserRepository $repository,
        UserFilter $filter
    ): JsonResponse
    {
        $users = $filter
            ->set($userQueryDto, $repository->createQueryBuilder('u'))
            ->filter()
            ->search()
            ->sort()
            ->paginate();

        return $this->json(
            $users,
            context: $filter->include($userQueryDto)
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        User $user,
        #[MapQueryString] GetUserQueryDto $queryDto,
        UserFilter $filter
    ): JsonResponse
    {
        return $this->json(
            $user,
            context: $filter->include($queryDto)
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateUserRequestDto $dto,
    ): JsonResponse
    {
        $user = $this->mapper->map($dto, User::class, 'user:write');

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(
            $user,
            status: Response::HTTP_CREATED,
            context: ['groups' => 'user:read']
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        User $user,
        #[MapUpdateRequestPayload(entityClass: User::class)] UpdateUserRequestDto $dto,
    ): JsonResponse
    {
        $user = $this->mapper->map($dto, $user, 'user:write');

        $this->em->flush();

        return $this->json(
            $user,
            context: ['groups' => 'user:read']
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(User $user): JsonResponse
    {
        $this->em->remove($user);
        $this->em->flush();

        return $this->json(null, status: Response::HTTP_NO_CONTENT);
    }
}
