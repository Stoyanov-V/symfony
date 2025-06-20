<?php

declare(strict_types=1);

namespace App\Story;

use App\Factory\CategoryFactory;
use Zenstruck\Foundry\Story;

final class DefaultCategoryStory extends Story
{
    public function build(): void
    {
        CategoryFactory::createMany(120);
    }
}
