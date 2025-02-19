<?php

namespace App\Story;

use App\Factory\RestaurantFactory;
use Zenstruck\Foundry\Story;

class DefaultRestaurantStory extends Story
{

    public function build(): void
    {
        RestaurantFactory::createMany(20);
    }
}
