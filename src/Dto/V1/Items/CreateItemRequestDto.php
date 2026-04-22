<?php

declare(strict_types=1);

namespace App\Dto\V1\Items;

use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;
final readonly class CreateItemRequestDto
{
    /**
     * @param  array<string, string>  $name
     * @param  array<string, string>|null  $description
     * @param array<string>|null $categoryIds
     */
    public function __construct(
        #[Assert\NotBlank]
        #[Assert\Type('array')]
        #[Assert\Count(min: 1)]
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\Length(min: 2, max: 255)
        ])]
        public array $name,

        #[Assert\NotBlank]
        #[Assert\PositiveOrZero]
        public string $price,

        #[Assert\NotBlank]
        public Uuid $restaurantId,

        #[Assert\Type('array')]
        #[Assert\All([
            new Assert\Type('string'),
            new Assert\Length(max: 2000)
        ])]
        public ?array $description = null,

        #[Assert\All([new Assert\Uuid()])]
        public ?array $categoryIds = null,
    ) {}
}
