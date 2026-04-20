<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Dto\V1\Auth\LoginRequestDto;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Service\AuthTokenManager;
use Psr\Cache\InvalidArgumentException;
use Random\RandomException;
use Symfony\Component\HttpFoundation\{Request, Response, JsonResponse};
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

#[Route('/api/v1/auth', name: 'api_v1_auth_', format: 'json', stateless: true)]
final class AuthController extends AbstractController
{
    public function __construct(
        private readonly AuthTokenManager $authTokenManager,
    ) {}

    #[Route('/login', name: 'login', methods: ['POST'])]
    public function login(
        #[MapRequestPayload] LoginRequestDto $dto,
        UserRepository $repository,
        UserPasswordHasherInterface $passwordHasher,
    ): JsonResponse
    {
        $user = $repository->findOneBy(['email' => $dto->email]);

        if (!$user || !$passwordHasher->isPasswordValid($user, $dto->password)) {
            return $this->json(['message' => 'Invalid credentials'], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $token = $this->authTokenManager->createToken($user->id->toRfc4122());
        } catch (InvalidArgumentException|RandomException) {
            return $this->json( ['message' => 'Failed to create token'], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return $this->json(['token' => $token]);
    }

    #[Route('/logout', name: 'logout', methods: ['POST'])]
    public function logout(Request $request): JsonResponse
    {
        $header = $request->headers->get('Authorization');

        if ($header && str_starts_with($header, 'Bearer ')) {

            $this->authTokenManager->invalidateToken(substr($header, 7));
        }

        return $this->json(
            null,
            status: Response::HTTP_NO_CONTENT
        );
    }

    #[Route('/me', name: 'me', methods: ['GET'])]
    public function me(#[CurrentUser] ?User $user): JsonResponse
    {
        return $this->json(
            $user,
            context: ['groups' => 'user:read']
        );
    }
}
