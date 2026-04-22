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
     *     name: array{en: string, de: string, bg: string}
     * }
     */
    protected function defaults(): array
    {
        return [
            'restaurant' => lazy(fn() => RestaurantFactory::random()),
            'name' => [
                'en' => self::faker()->words(2, true),
                'de' => self::faker()->words(2, true),
                'bg' => self::faker()->words(2, true),
            ],
        ];
    }

    public static function class(): string
    {
        return Category::class;
    }
}
