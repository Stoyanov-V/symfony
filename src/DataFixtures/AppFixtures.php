<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\DefaultUserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultUserStory::load();
    }
}
