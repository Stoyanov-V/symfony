<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
final class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['category:read', 'user:read:with-restaurants'])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(['category:read', 'user:read:with-restaurants'])]
    private ?string $name = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'restaurants')]
    private Collection $users;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'restaurant')]
    private Collection $categories;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'restaurant', orphanRemoval: true)]
    private Collection $items;

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->items = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(?int $id): Restaurant
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): Restaurant
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, User>
     * @noinspection PhpUnused
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): Restaurant
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
        }

        return $this;
    }

    public function removeUser(User $user): Restaurant
    {
        $this->users->removeElement($user);

        return $this;
    }

    /**
     * @return Collection<int, Category>
     * @noinspection PhpUnused
     */
    public function getCategories(): Collection
    {
        return $this->categories;
    }

    /** @noinspection PhpUnused */
    public function addCategory(Category $category): Restaurant
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
            $category->setRestaurant($this);
        }

        return $this;
    }

    /** @noinspection PhpUnused */
    public function removeCategory(Category $category): Restaurant
    {
        if ($this->categories->removeElement($category)) {
            // set the owning side to null (unless already changed)
            if ($category->getRestaurant() === $this) {
                $category->setRestaurant(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Item>
     * @noinspection PhpUnused
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /** @noinspection PhpUnused */
    public function addItem(Item $item): Restaurant
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setRestaurant($this);
        }

        return $this;
    }

    /** @noinspection PhpUnused */
    public function removeItem(Item $item): Restaurant
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getRestaurant() === $this) {
                $item->setRestaurant(null);
            }
        }

        return $this;
    }
}
