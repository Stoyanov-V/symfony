<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Attribute\MapUpdateRequestPayload;
use App\Service\Mappers\CategoryMapper;
use App\Dto\V1\Categories\{CreateCategoryRequestDto, GetCategoryQueryDto, UpdateCategoryRequestDto};
use App\Entity\Category;
use App\Filters\V1\CategoryFilter;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{JsonResponse, Response};
use Symfony\Component\HttpKernel\Attribute\{Cache, MapQueryString, MapRequestPayload};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[Route('/api/v1/categories', name: 'api_v1_categories_', format: 'json', stateless: true)]
final class CategoriesController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CategoryMapper $mapper,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    #[Cache(maxage: 60, smaxage: 3600, public: true)]
    public function index(
        #[MapQueryString] GetCategoryQueryDto $queryDto,
        CategoryRepository $repository,
        CategoryFilter $filter,
    ): JsonResponse
    {
        $categories = $filter
            ->set($queryDto, $repository->createQueryBuilder('c'))
            ->filter()
            ->search()
            ->sort()
            ->paginate();

        return $this->json(
            $categories,
            context: $filter->include($queryDto)
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    #[Cache(maxage: 60, smaxage: 3600, public: true)]
    public function show(
        Category $category,
        #[MapQueryString] GetCategoryQueryDto $queryDto,
        CategoryFilter $filter
    ): JsonResponse
    {
        return $this->json(
            $category,
            context: $filter->include($queryDto)
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateCategoryRequestDto $dto,
    ): JsonResponse
    {
        $category = $this->mapper->map($dto, Category::class, 'category:write');

        $this->em->persist($category);
        $this->em->flush();

        return $this->json(
            $category,
            status: Response::HTTP_CREATED,
            context: ['groups' => 'category:read']
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        Category $category,
        #[MapUpdateRequestPayload(entityClass: Category::class)] UpdateCategoryRequestDto $dto,
    ): JsonResponse
    {
        $category = $this->mapper->map($dto, $category, 'category:write');

        $this->em->flush();

        return $this->json(
            $category,
            context: ['groups' => 'category:read']
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Category $category): JsonResponse
    {
        $this->em->remove($category);
        $this->em->flush();

        return $this->json(null, status: Response::HTTP_NO_CONTENT);
    }
}
