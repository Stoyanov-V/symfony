<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\ItemRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Item
{
    use TimestampableTrait;

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

    /** @var array<string, string> */
    #[ORM\Column(type: Types::JSON)]
    #[Groups([
        'item:read',
        'item:write',
        'restaurant:read:with-items',
        'category:read:with-items',
    ])]
    public array $name = [] {
        set {
            $this->name = array_map(trim(...), $value);
        }
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

    /** @var array<string, string>|null */
    #[ORM\Column(type: Types::JSON, nullable: true)]
    #[Groups([
        'item:read',
        'item:write',
        'restaurant:read:with-items',
        'category:read:with-items',
    ])]
    public ?array $description = null;

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
