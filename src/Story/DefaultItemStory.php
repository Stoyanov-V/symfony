<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\ItemFactory;
use Zenstruck\Foundry\Story;

final class DefaultItemStory extends Story
{
    public function build(): void
    {
        ItemFactory::createMany(500);
    }
}
