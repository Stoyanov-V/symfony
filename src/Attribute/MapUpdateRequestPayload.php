<?php

declare(strict_types=1);

namespace App\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_PARAMETER)]
final class MapUpdateRequestPayload
{
    public function __construct(
        public string $putGroup = 'put',
        public string $patchGroup = 'patch',
        public ?string $entityClass = null,
    )
    {}
}
