<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentProxyObjectFactory<User>
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
            'email' => self::faker()->unique()->email(),
            'firstName' => self::faker()->firstName(),
            'lastName' => self::faker()->lastName(),
            'password' => self::faker()->password(),
            'restaurants' => lazy(fn() => RestaurantFactory::randomRange(1,3))
        ];
    }

    public static function class(): string
    {
        return User::class;
    }
}
