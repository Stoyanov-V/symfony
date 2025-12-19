<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Restaurant;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

/**
 * @extends PersistentObjectFactory<Restaurant>
 */
final class RestaurantFactory extends PersistentObjectFactory
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
