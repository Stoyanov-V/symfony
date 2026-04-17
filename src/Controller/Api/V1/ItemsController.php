<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Attribute\MapUpdateRequestPayload;
use App\Dto\V1\Items\{GetItemQueryDto, CreateItemRequestDto, UpdateItemRequestDto};
use App\Entity\Item;
use App\Filters\V1\ItemFilter;
use App\Repository\ItemRepository;
use App\Service\Mappers\ItemMapper;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\HttpKernel\Attribute\{MapQueryString, MapRequestPayload};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route('/api/v1/items', name: 'api_v1_items_', format: 'json', stateless: true)]
final class ItemsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly ItemMapper $mapper,
    ) {}
    /**
     * @throws Exception
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString] GetItemQueryDto $queryDto,
        ItemRepository $repository,
        ItemFilter $filter,
    ): JsonResponse
    {
        $items = $filter
            ->set($queryDto, $repository->createQueryBuilder('i'))
            ->filter()
            ->sort()
            ->paginate();

        return $this->json(
            $items,
            context: $filter->include($queryDto)
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(
        Item $item,
        #[MapQueryString] GetItemQueryDto $queryDto,
        ItemFilter $filter,
    ): JsonResponse
    {
        return $this->json(
            $item,
            context: $filter->include($queryDto)
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateItemRequestDto $dto,
    ): JsonResponse
    {
        $item = $this->mapper->map($dto, Item::class, 'item:write');

        $this->em->persist($item);
        $this->em->flush();

        return $this->json(
            $item,
            status: Response::HTTP_CREATED,
            context: ['groups' => 'item:read']
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        Item $item,
        #[MapUpdateRequestPayload(entityClass: Item::class)] UpdateItemRequestDto $dto,
    ): JsonResponse
    {
        $item = $this->mapper->map($dto, $item, 'item:write');

        $this->em->flush();

        return $this->json(
            $item,
            context: ['groups' => 'item:read']
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Item $item): JsonResponse
    {
        $this->em->remove($item);
        $this->em->flush();

        return $this->json(null, status: Response::HTTP_NO_CONTENT);
    }
}
