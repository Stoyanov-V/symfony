<?php

declare(strict_types=1);

namespace App\Factory;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Persistence\PersistentObjectFactory;
use Zenstruck\Foundry\LazyValue;
use function Zenstruck\Foundry\lazy;

/**
 * @extends PersistentObjectFactory<User>
 */
final class UserFactory extends PersistentObjectFactory
{
    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
    }

    /**
     * @return array{
     *     email: string,
     *     name: string,
     *     roles: array<string>,
     *     password: LazyValue,
     *     restaurants: LazyValue
     * }
     */
    protected function defaults(): array
    {
        return [
            'email' => self::faker()->unique()->email(),
            'name' => self::faker()->name(),
            'roles' => ['ROLE_USER'],
            'password' => lazy(
                fn() => $this->passwordHasher->hashPassword(new User(), self::faker()->password())
            ),
            'restaurants' => lazy(fn() => RestaurantFactory::randomRange(1,3))
        ];
    }

    public static function class(): string
    {
        return User::class;
    }
}
