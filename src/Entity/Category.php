<?php

declare(strict_types=1);

namespace App\Entity;

use App\Entity\Traits\TimestampableTrait;
use App\Repository\CategoryRepository;
use Doctrine\Common\Collections\{Collection, ArrayCollection};
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Uid\Uuid;

#[ORM\Entity(repositoryClass: CategoryRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Category
{
    use TimestampableTrait;

    #[ORM\Id]
    #[ORM\Column(type: UuidType::NAME, unique: true)]
    #[ORM\GeneratedValue(strategy: 'CUSTOM')]
    #[ORM\CustomIdGenerator(class: 'doctrine.uuid_generator')]
    #[Groups([
        'category:read',
        'restaurant:read:with-categories',
        'item:read:with-categories',
    ])]
    private(set) ?Uuid $id = null;

    #[ORM\Column(length: 255)]
    #[Groups([
        'category:read',
        'category:write',
        'restaurant:read:with-categories',
        'item:read:with-categories',
    ])]
    public string $name {
        set => trim($value);
    }

    #[ORM\ManyToOne(inversedBy: 'categories')]
    #[Groups([
        'category:write',
        'category:read:with-restaurant',
    ])]
    public ?Restaurant $restaurant = null;


    /**
     * @var Collection<int, Item>
     */
    #[ORM\ManyToMany(targetEntity: Item::class, mappedBy: 'categories')]
    #[Groups([
        'category:read:with-items',
    ])]
    private(set) Collection $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
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
