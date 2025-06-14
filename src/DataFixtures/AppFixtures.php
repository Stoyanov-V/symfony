<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Story\DefaultCategoryStory;
use App\Story\DefaultItemStory;
use App\Story\DefaultRestaurantStory;
use App\Story\DefaultUserStory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

final class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        DefaultRestaurantStory::load();
        DefaultUserStory::load();
        DefaultCategoryStory::load();
        DefaultItemStory::load();
    }
}
