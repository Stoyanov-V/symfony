<?php

declare(strict_types=1);

namespace App\Dto\V1\Users;

use App\Entity\User;
use ArrayAccess;
use ArrayIterator;
use IteratorAggregate;
use JsonSerializable;
use LogicException;
use Symfony\Component\Validator\Constraints as Assert;
use Traversable;

/**
 * @implements ArrayAccess<string, mixed>
 * @implements IteratorAggregate<string, mixed>
 */
final class UpdateUserRequestDto implements ArrayAccess, IteratorAggregate, JsonSerializable
{
    /**
     * @param  ?string  $name
     * @param  ?string  $email
     * @param  ?array<string>  $roles
     */
    public function __construct(
        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\Length(min: 3, max: 255, groups: ['put', 'patch'])]
        public ?string $name = null,

        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\Length(max: 180, groups: ['put', 'patch'])]
        #[Assert\Email(groups: ['put', 'patch'])]
        public ?string $email = null,

        #[Assert\Type('array')]
        #[Assert\NotBlank(groups: ['put', 'patch'])]
        #[Assert\All([
            new Assert\Type('string', groups: ['put', 'patch']),
            new Assert\Choice(
                choices: ['ROLE_USER', 'ROLE_ADMIN', 'ROLE_MANAGER'],
                groups: ['put', 'patch']
            ),
        ])]
        public ?array $roles = null,
    ) {}

    /** @noinspection PhpUnused */
    public static function fromEntity(User $user): self
    {
        return new self(
            name: $user->getName(),
            email: $user->getEmail(),
            roles: $user->getRoles(),
        );
    }

    public function offsetExists(mixed $offset): bool
    {
        return array_key_exists($offset, $this->toArray());
    }

    public function offsetGet(mixed $offset): mixed
    {
        return $this->toArray()[$offset] ?? null;
    }

    public function offsetSet(mixed $offset, mixed $value): void
    {
        throw new LogicException('Cannot modify readonly DTO');
    }

    public function offsetUnset(mixed $offset): void
    {
        throw new LogicException('Cannot unset properties of readonly DTO');
    }

    public function getIterator(): Traversable
    {
        return new ArrayIterator($this->toArray());
    }

    /**
     * @return array<string, mixed>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * @return array<string, mixed>
     */
    private function toArray(): array
    {
        return get_object_vars($this);
    }
}
