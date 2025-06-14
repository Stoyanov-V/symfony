<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Item;
use App\Entity\Restaurant;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentProxyObjectFactory<Item>
 */
final class ItemFactory extends PersistentProxyObjectFactory
{
    /**
     * @return array{
     *     name: string,
     *     price: float,
     *     description: ?string,
     *     restaurant: Restaurant
     * }
     */
    protected function defaults(): array
    {
        return [
            'name' => self::faker()->words(2, true),
            'price' => self::faker()->randomFloat(2, 10, 100),
            'description' => rand(0, 1) ? self::faker()->sentence(10) : null,
            // @phpstan-ignore-next-line
            'restaurant' => lazy(fn() => RestaurantFactory::random()),
        ];
    }

    public static function class(): string
    {
        return Item::class;
    }
}
