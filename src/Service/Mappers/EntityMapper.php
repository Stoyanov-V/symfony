<?php

declare(strict_types=1);

namespace App\Service\Mappers;

use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\{AbstractNormalizer, DenormalizerInterface};

abstract readonly class EntityMapper
{
    public function __construct(
        private DenormalizerInterface $denormalizer,
    )
    {}

    /**
     * @template T of object
     * @param  class-string<T>|T  $targetEntity  Class string for create, object for update
     * @return T
     * @throws ExceptionInterface
     */
    public function map(object $dto, string|object $targetEntity, string $group): object
    {
        $context = ['groups' => $group];

        if (is_object($targetEntity)) {
            $context[AbstractNormalizer::OBJECT_TO_POPULATE] = $targetEntity;
        }

        $entity = $this->denormalizer->denormalize(
            $dto,
            type: is_object($targetEntity) ? $targetEntity::class : $targetEntity,
            context: $context
        );

        $this->handleAssociations($dto, $entity);

        return $entity;
    }

    abstract protected function handleAssociations(object $dto, object $entity): void;
}
