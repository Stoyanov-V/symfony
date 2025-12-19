<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;

use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentObjectFactory<Category>
 */
final class CategoryFactory extends PersistentObjectFactory
{
    /**
     * @return array{
     *     restaurant: object,
     *     name: string
     * }
     */
    protected function defaults(): array
    {
        return [
            'restaurant' => lazy(fn() => RestaurantFactory::random()),
            'name' => self::faker()->words(2, true),
        ];
    }

    public static function class(): string
    {
        return Category::class;
    }
}
