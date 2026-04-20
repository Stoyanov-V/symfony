<?php

declare(strict_types=1);

namespace App\Service;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Random\RandomException;
use RuntimeException;
use Symfony\Component\DependencyInjection\Attribute\Target;

readonly class AuthTokenManager
{
    private const int|float EXPIRES_IN = 3600 * 24;

    public function __construct(
        #[Target('auth_token_storage')]
        private CacheItemPoolInterface $authTokenStorage,
    ) {}

    /**
     * @throws RandomException
     * @throws InvalidArgumentException
     */
    public function createToken(string $userId): string
    {
        $token = bin2hex(random_bytes(32));
        $item = $this->authTokenStorage->getItem($token);
        $item->set($userId);
        $item->expiresAfter(self::EXPIRES_IN);

        if (!$this->authTokenStorage->save($item)) {
            throw new RuntimeException('Token storage failed');
        }

        return $token;
    }

    public function validateToken(string $token): ?string
    {
        if (!ctype_xdigit($token)) {
            return null;
        }

        try {
            $item = $this->authTokenStorage->getItem($token);
        } catch (InvalidArgumentException) {
            return null;
        }

        if (!$item->isHit()) {
            return null;
        }

        $value = $item->get();

        return is_string($value) ? $value : null;
    }

    public function invalidateToken(string $token): void
    {
        try {
            $this->authTokenStorage->deleteItem($token);
        } catch (InvalidArgumentException) {}
    }
}
