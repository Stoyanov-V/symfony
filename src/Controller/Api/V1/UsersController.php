<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Attribute\MapUpdateRequestPayload;
use App\Dto\V1\Users\CreateUserRequestDto;
use App\Dto\V1\Users\GetUserQueryDto;
use App\Dto\V1\Users\UpdateUserRequestDto;
use App\Entity\User;
use App\Filters\V1\UserFilter;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;

#[Route('/api/v1/users', name: 'api_v1_users_', format: 'json', stateless: true)]
final class UsersController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DenormalizerInterface $denormalizer,
    )
    {}

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
            ->sort()
            ->paginate();

        $context = $filter->include($userQueryDto);

        return $this->json(
            $users,
            status: Response::HTTP_OK,
            context: $context
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(User $user): JsonResponse
    {
        return $this->json(
            $user,
            status: Response::HTTP_OK,
            context: ['groups' => 'user:read']
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateUserRequestDto $dto,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse
    {
        $user = $this->denormalizer->denormalize(
            $dto,
            type: User::class,
            context: ['groups' => 'user:write'],
        );

        $hashedPassword = $passwordHasher->hashPassword(
            $user,
            $dto->password
        );
        $user->setPassword($hashedPassword);

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
        $user = $this->denormalizer->denormalize(
            $dto,
            type: User::class,
            context: [
                AbstractNormalizer::OBJECT_TO_POPULATE => $user,
                'groups' => 'user:write',
            ],
        );

        $this->em->persist($user);
        $this->em->flush();

        return $this->json(
            $user,
            status: Response::HTTP_OK,
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
