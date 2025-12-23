<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\ORM\Mapping as ORM;
use SensitiveParameter;
use Symfony\Component\Security\Core\User\{UserInterface, PasswordAuthenticatedUserInterface};
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
    private(set) ?int $id = null;

    #[ORM\Column(type: 'string', length: 180, unique: true)]
    #[Groups([
        'user:read',
        'user:write',
        'restaurant:read:with-users',
    ])]
    public string $email {
        get => $this->email;
        set => $this->email = $value;
    }

    #[ORM\Column(type: 'string')]
    #[Groups([
        'user:read',
        'user:write',
        'restaurant:read:with-users',
    ])]
    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    /** @var array<string> $roles */
    #[ORM\Column(type: 'json')]
    #[Groups(['user:write'])]
    public array $roles = [] {
        set => $this->roles = array_values($value);
    }

    #[ORM\Column(type: 'string')]
    #[Groups(['user:write'])]
    private string $password;

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

    public function setPassword(#[SensitiveParameter] string $password): User
    {
        $this->password = $password;
        return $this;
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

    /** @noinspection PhpUnused */
    public function eraseCredentials(): void
    {
        // TODO: Implement eraseCredentials() method.
    }

    public function getUserIdentifier(): string
    {
        return $this->email;
    }
}
