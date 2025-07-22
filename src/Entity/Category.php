<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
class Category
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('restaurant:read:with-categories')]
    private int $id;

    #[ORM\ManyToOne(inversedBy: 'categories')]
    private ?Restaurant $restaurant = null;

    #[ORM\Column(length: 255)]
    #[Groups('restaurant:read:with-categories')]
    private string $name;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'categories')]
    private Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): Category
    {
        $this->id = $id;
        return $this;
    }

    public function getRestaurant(): ?Restaurant
    {
        return $this->restaurant;
    }

    public function setRestaurant(?Restaurant $restaurant): Category
    {
        $this->restaurant = $restaurant;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): Category
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /** @noinspection PhpUnused */
    public function addItem(Item $item): Category
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->addCategory($this);
        }

        return $this;
    }

    /** @noinspection PhpUnused */
    public function removeItem(Item $item): Category
    {
        if ($this->items->removeElement($item)) {
            $item->removeCategory($this);
        }

        return $this;
    }
}
