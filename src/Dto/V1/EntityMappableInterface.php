<?php

declare (strict_types = 1);

namespace App\Dto\V1;

interface EntityMappableInterface
{
    public static function fromEntity(object $entity): self;
}
