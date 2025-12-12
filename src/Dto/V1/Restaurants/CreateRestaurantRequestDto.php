<?php

declare(strict_types=1);

namespace App\Dto\V1\Restaurants;

use Symfony\Component\Validator\Constraints as Assert;

final readonly class CreateRestaurantRequestDto
{
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Length(min: 2, max: 255)]
        public string $name,
    )
    {
    }
}
