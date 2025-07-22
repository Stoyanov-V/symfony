<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    #[ORM\Column(type: 'integer')]
    #[Groups([
        'user:read',
        'restaurant:read:with-users'
    ])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups([
        'user:read',
        'user:write',
        'restaurant:read:with-users',
    ])]
    private string $email;

    #[ORM\Column(type: 'string')]
    #[Groups([
        'user:read',
        'user:write',
        'restaurant:read:with-users',
    ])]
    private string $name;

    /** @var array<string> $roles */
    #[ORM\Column(type: 'json')]
    #[Groups(['user:write'])]
    private array $roles = [];

    #[ORM\Column(type: 'string')]
    #[Groups(['user:write'])]
    private string $password;

    /**
     * @var Collection<int, Restaurant>
     */
    #[ORM\ManyToMany(targetEntity: Restaurant::class, mappedBy: 'users')]
    #[Groups(['user:read:with-restaurants'])]
    private Collection $restaurants;

    public function __construct()
    {
        $this->restaurants = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): User
    {
        $this->id = $id;
        return $this;
    }

    /** @noinspection PhpUnused */
    public function getEmail(): string
    {
        return $this->email;
    }

    /** @noinspection PhpUnused */
    public function setEmail(string $email): User
    {
        $this->email = $email;
        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): User
    {
        $this->name = $name;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(#[SensitiveParameter] string $password): User
    {
        $this->password = $password;
        return $this;
    }

    /**
     * @return Collection<int, Restaurant>
     * @noinspection PhpUnused
     */
    public function getRestaurants(): Collection
    {
        return $this->restaurants;
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
        $roles = $this->roles;

        $roles[] =  'ROLE_USER';

        return array_unique($roles);
    }

    /**
     * @param array<string> $roles
     * @noinspection PhpUnused
     */
    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
