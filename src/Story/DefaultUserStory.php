<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\RestaurantFactory;
use App\Factory\UserFactory;
use Zenstruck\Foundry\Story;

use function Zenstruck\Foundry\lazy;

final class DefaultUserStory extends Story
{
    public function build(): void
    {
        UserFactory::createMany(10);
    }
}
