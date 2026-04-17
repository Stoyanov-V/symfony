<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
class Item
{
    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'item:read',
        'item:write',
        'restaurant:read:with-items',
        'category:read:with-items',
    ])]
    private(set) ?Uuid $id = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups([
        'item:write',
        'item:read:with-restaurant',
    ])]
    public ?Restaurant $restaurant = null;

    /**
     * @var Collection<int, Category>
     */
    #[ORM\ManyToMany(targetEntity: Category::class, inversedBy: 'items')]
    #[Groups([
        'item:write',
        'item:read:with-categories'
    ])]
    private(set) Collection $categories;

    #[ORM\Column(length: 255)]
    #[Groups([
        'item:read',
        'item:write',
        'restaurant:read:with-items',
        'category:read:with-items',
    ])]
    public string $name {
        set => $this->name = trim($value);
    }

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Groups([
        'item:read',
        'item:write',
        'restaurant:read:with-items',
        'category:read:with-items',
    ])]
    public string $price {
        set {
            if ((float) $value < 0) {
                throw new InvalidArgumentException("Price cannot be negative.");
            }
            $this->price = (string) $value;
        }
    }

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups([
        'item:read',
        'item:write',
        'restaurant:read:with-items',
        'category:read:with-items',
    ])]
    public ?string $description = null;

    public function __construct()
    {
        $this->categories = new ArrayCollection();
    }

    public function addCategory(Category $category): Item
    {
        if (!$this->categories->contains($category)) {
            $this->categories->add($category);
        }

        return $this;
    }

    public function removeCategory(Category $category): Item
    {
        $this->categories->removeElement($category);

        return $this;
    }
}
