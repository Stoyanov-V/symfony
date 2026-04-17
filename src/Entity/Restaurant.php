<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\RestaurantRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: RestaurantRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Restaurant
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'restaurant:read',
        'category:read',
        'user:read:with-restaurants',
        'item:read:with-restaurant',
    ])]
    private(set) ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'restaurant:read',
        'restaurant:write',
        'category:read',
        'user:read:with-restaurants',
        'item:read:with-restaurant',
    ])]
    public string $name {
        set => trim($value);
    }

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'restaurants')]
    #[Groups(['restaurant:read:with-users'])]
    private(set) Collection $users;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\OneToMany(targetEntity: Category::class, mappedBy: 'restaurant')]
    #[Groups(['restaurant:read:with-categories'])]
    private(set) Collection $categories;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'restaurant', orphanRemoval: true)]
    #[Groups(['restaurant:read:with-items'])]
    private(set) Collection $items;

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
            $category->restaurant = $this;
        }

        return $this;
    }

    /** @noinspection PhpUnused */
    public function removeCategory(Category $category): Restaurant
    {
        if ($this->categories->removeElement($category)) {
            if ($category->restaurant === $this) {
                $category->restaurant = null;
            }
        }

        return $this;
    }

    /** @noinspection PhpUnused */
    public function addItem(Item $item): Restaurant
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->restaurant = $this;
        }

        return $this;
    }

    /** @noinspection PhpUnused */
    public function removeItem(Item $item): Restaurant
    {
        if ($this->items->removeElement($item)) {
            if ($item->restaurant === $this) {
                $item->restaurant = null;
            }
        }

        return $this;
    }
}
