<?php

declare(strict_types=1);

namespace App\Resolver;

use App\Attribute\MapUpdateRequestPayload;
use Doctrine\Persistence\ManagerRegistry;
use InvalidArgumentException;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Autoconfigure(tags: ['controller.argument_value_resolver'])]
final readonly class UpdateRequestPayloadResolver implements ValueResolverInterface
{
    public function __construct(
        private SerializerInterface $serializer,
        private ValidatorInterface $validator,
        private ManagerRegistry $registry,
    )
    {}

    /**
     * @return iterable<object>
     * @throws ExceptionInterface
     */
    public function resolve(Request $request, ArgumentMetadata $argument): iterable
    {
        $attribute = $argument->getAttributesOfType(MapUpdateRequestPayload::class, ArgumentMetadata::IS_INSTANCEOF)[0] ?? null;

        if (is_null($attribute)) {
            return [];
        }

        $dtoClass = $argument->getType() ?? '';

        if (!class_exists($dtoClass)) {
            throw new InvalidArgumentException('Class does not exist');
        }

        $method = $request->getMethod();

        $group = match ($method) {
            'PUT'   => $attribute->putGroup,
            'PATCH' => $attribute->patchGroup,
            default => null,
        };

        if (is_null($group)) {
            return [];
        }

        if ($method === Request::METHOD_PATCH) {

            $entity = $this->getEntity($request, $attribute);

            $dto = $this->deserialize(
                $request,
                $dtoClass,
                [AbstractNormalizer::OBJECT_TO_POPULATE => $dtoClass::fromEntity($entity)],
            );

        } else {
            $dto = $this->deserialize($request, $dtoClass);
        }

        $this->validate($dto, $group);

        yield $dto;
    }

    /**
     * @param array<string, mixed> $context
     * @throws ExceptionInterface
     */
    private function deserialize(Request $request, string $type, array $context = []): mixed
    {
        return $this->serializer->deserialize(
            $request->getContent(),
            $type,
            $request->getContentTypeFormat() ?? 'json',
            $context,
        );
    }

    private function getEntity(Request $request, MapUpdateRequestPayload $attribute): object
    {
        $id = $request->attributes->get('id');
        $entityClass = $attribute->entityClass;

        if (!class_exists($entityClass)) {
            throw new InvalidArgumentException('Entity class does not exist');
        }

        $entity = $this->registry
            ->getRepository($entityClass)
            ->find($id);

        if (!$entity) {
            throw new NotFoundHttpException('Related entity not found.');
        }

        return $entity;
    }

    private function validate(object $dto, string $group): void
    {
        $violations = $this->validator->validate($dto, null, $group ? [$group] : []);

        if (count($violations) > 0) {
            $errors = [];
            foreach ($violations as $violation) {
                $errors[$violation->getPropertyPath()] = $violation->getMessage();
            }

            throw new BadRequestHttpException(json_encode(['errors' => $errors]));
        }
    }
}
