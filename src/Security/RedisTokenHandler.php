<?php

declare(strict_types=1);

namespace App\Security;

use App\Repository\UserRepository;
use App\Service\AuthTokenManager;
use SensitiveParameter;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Http\AccessToken\AccessTokenHandlerInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;

final readonly class RedisTokenHandler implements AccessTokenHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository,
        private AuthTokenManager $authTokenManager,
    ) {

    }

    public function getUserBadgeFrom(#[SensitiveParameter] string $accessToken): UserBadge
    {
        $userId = $this->authTokenManager->validateToken($accessToken);

        if (null === $userId) {
            throw new BadCredentialsException('Invalid token or expired token.');
        }

        return new UserBadge($userId, fn($id) => $this->userRepository->find($id));
    }
}
