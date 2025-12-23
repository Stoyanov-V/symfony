<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
class Restaurant
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups([
        'restaurant:read',
        'category:read',
        'user:read:with-restaurants'
    ])]
    private(set) ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'restaurant:read',
        'restaurant:write',
        'category:read',
        'user:read:with-restaurants'
    ])]
    public string $name {
        get => $this->name;
        set => $this->name = $value;
    }

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'restaurants')]
    #[Groups(['restaurant:read:with-users'])]
    private Collection $users {
        get {
            return $this->users;
        }
    }

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'restaurant')]
    #[Groups(['restaurant:read:with-categories'])]
    private Collection $categories {
        get {
            return $this->categories;
        }
    }

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'restaurant', orphanRemoval: true)]
    #[Groups(['restaurant:read:with-items'])]
    private Collection $items {
        get {
            return $this->items;
        }
    }

    public function __construct()
    {
        $this->users = new ArrayCollection();
        $this->categories = new ArrayCollection();
        $this->items = new ArrayCollection();
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
            if ($category->getRestaurant() === $this) {
                $category->setRestaurant(null);
            }
        }

        return $this;
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
            if ($item->getRestaurant() === $this) {
                $item->setRestaurant(null);
            }
        }

        return $this;
    }
}
