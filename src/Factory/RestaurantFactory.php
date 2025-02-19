<?php

namespace App\Factory;

use App\Entity\Restaurant;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Restaurant>
 */
final class RestaurantFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array{
     *     'name': string,
     * }
     */
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->company(),
        ];
    }

    public static function class(): string
    {
        return Restaurant::class;
    }
}
