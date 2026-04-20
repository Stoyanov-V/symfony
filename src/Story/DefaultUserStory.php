<?php

declare(strict_types=1);

namespace App\Story;

use App\Entity\User;
use App\Factory\UserFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Zenstruck\Foundry\Story;


final class DefaultUserStory extends Story
{
    public function __construct(
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}
    public function build(): void
    {
        UserFactory::createMany(15);

        UserFactory::createOne([
            'email' => 'admin@email.com',
            'name' => 'System Admin',
            'roles' => ['ROLE_ADMIN'],
            'password' => $this->passwordHasher->hashPassword(new User(), 'p@$$word'),
            'restaurants' => [],
        ]);
    }
}
