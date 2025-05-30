<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Zenstruck\Foundry\LazyValue;
use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentProxyObjectFactory<User>
 */
final class UserFactory extends PersistentProxyObjectFactory
{

    /**
     * @return array{
     *     'email': string,
     *     'name': string,
     *     'password': string,
     *      restaurants: LazyValue
     * }
     */
    protected function defaults(): array
    {
        return [
            'email' => self::faker()->unique()->email(),
            'name' => self::faker()->name(),
            'password' => self::faker()->password(),
            'restaurants' => lazy(fn() => RestaurantFactory::randomRange(1,3))
        ];
    }

    public static function class(): string
    {
        return User::class;
    }
}
