<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Item;
use Zenstruck\Foundry\LazyValue;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentObjectFactory<Item>
 */
final class ItemFactory extends PersistentObjectFactory
{
    /**
     * @return array{
     *     name: string,
     *     price: float,
     *     description: ?string,
     *     restaurant: LazyValue
     * }
     */
    protected function defaults(): array
    {
        return [
            'name' => (string) self::faker()->words(2, true),
            'price' => self::faker()->randomFloat(2, 10, 100),
            'description' => rand(0, 1) ? self::faker()->sentence(10) : null,
            'restaurant' => lazy(fn() => RestaurantFactory::random()),
        ];
    }

    public static function class(): string
    {
        return Item::class;
    }
}
