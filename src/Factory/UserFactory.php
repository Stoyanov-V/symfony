<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @template T of object
 * @extends PersistentProxyObjectFactory <T>
 */
final class UserFactory extends PersistentProxyObjectFactory
{

    /**
     * @return array{
     *     'email': string,
     *     'firstName': string,
     *     'lastName': string,
     *     'password': string
     * }
     */
    protected function defaults(): array
    {
        return [
            'email' => self::faker()->email(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'password' => self::faker()->password(),
        ];
    }

    public static function class(): string
    {
        return User::class;
    }
}
