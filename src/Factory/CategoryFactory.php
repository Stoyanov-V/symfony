<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\Category;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentProxyObjectFactory<Category>
 */
final class CategoryFactory extends PersistentProxyObjectFactory
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
            // @phpstan-ignore-next-line
            'restaurant' => lazy(fn() => RestaurantFactory::random()),
            'name' => self::faker()->words(2, true),
        ];
    }

    public static function class(): string
    {
        return Category::class;
    }
}
