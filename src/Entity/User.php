<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Security\Core\User\{UserInterface, PasswordAuthenticatedUserInterface};
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\HasLifecycleCallbacks]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'user:read',
        'restaurant:read:with-users'
    ])]
    private(set) ?Uuid $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups([
        'user:read',
        'user:write',
        'restaurant:read:with-users',
    ])]
    public string $email {
        set => $this->email = $value
                |> trim(...)
                |> strtolower(...);
    }

    #[ORM\Column(type: 'string')]
    #[Groups([
        'user:read',
        'user:write',
        'restaurant:read:with-users',
    ])]
    public string $name {
        set => $this->name = trim($value);
    }

    /** @var array<string> $roles */
    #[ORM\Column(type: 'json')]
    #[Groups(['user:write'])]
    public array $roles = [] {
        get => $this->roles
                |>array_unique(...)
                |>array_values(...);
        set => $this->roles = array_values($value);
    }

    #[ORM\Column(type: 'string')]
    #[Groups(['user:write'])]
    public string $password {
        get => $this->password;
        set => $this->password = $value;
    }

    /**
     * @var Collection<int, Restaurant>
     */
    #[ORM\ManyToMany(targetEntity: Restaurant::class, mappedBy: 'users')]
    #[Groups(['user:read:with-restaurants'])]
    public Collection $restaurants {
        get {
            return $this->restaurants;
        }
    }

    public function __construct()
    {
        $this->restaurants = new ArrayCollection();
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    /** @noinspection PhpUnused */
    public function addRestaurant(Restaurant $restaurant): User
    {
        if (!$this->restaurants->contains($restaurant)) {
            $this->restaurants->add($restaurant);
            $restaurant->addUser($this);
        }

        return $this;
    }

    /** @noinspection PhpUnused */
    public function removeRestaurant(Restaurant $restaurant): User
    {
        if ($this->restaurants->removeElement($restaurant)) {
            $restaurant->removeUser($this);
        }

        return $this;
    }

    public function getRoles(): array
    {

        return $this->roles;
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
