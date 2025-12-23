<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Attribute\MapUpdateRequestPayload;
use App\Dto\V1\Restaurants\{CreateRestaurantRequestDto, GetRestaurantsQueryDto, UpdateRestaurantRequestDto};
use App\Entity\Restaurant;
use App\Filters\V1\RestaurantFilter;
use App\Repository\RestaurantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\{Response, JsonResponse};
use Symfony\Component\HttpKernel\Attribute\{MapQueryString, MapRequestPayload};
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Serializer\Exception\ExceptionInterface;
use Symfony\Component\Serializer\Normalizer\{AbstractNormalizer, DenormalizerInterface};


#[Route('/api/v1/restaurants', name: 'api_v1_restaurants_', format: 'json', stateless: true)]
final class RestaurantsController extends AbstractController
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DenormalizerInterface $denormalizer,
    ) {}

    /**
     * @throws Exception
     */
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(
        #[MapQueryString] GetRestaurantsQueryDto $restaurantsQueryDto,
        RestaurantRepository $repository,
        RestaurantFilter $filter
    ): JsonResponse
    {
        $restaurants = $filter
            ->set($restaurantsQueryDto, $repository->createQueryBuilder('r'))
            ->filter()
            ->sort()
            ->paginate();

        $context = $filter->include($restaurantsQueryDto);

        return $this->json(
            $restaurants,
            status: Response::HTTP_OK,
            context: $context
        );
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(Restaurant $restaurant): JsonResponse
    {
        return $this->json(
            $restaurant,
            status: Response::HTTP_OK,
            context: ['groups' => 'restaurant:read']
        );
    }

    /**
     * @throws ExceptionInterface
     */
    #[Route('/', name: 'create', methods: ['POST'])]
    public function create(
        #[MapRequestPayload] CreateRestaurantRequestDto $dto,
    ): JsonResponse
    {
        $restaurant = $this->denormalizer->denormalize(
            $dto,
            type: Restaurant::class,
            context: ['groups' => 'restaurant:write']
        );

        $this->em->persist($restaurant);
        $this->em->flush();

        return $this->json(
            $restaurant,
            status: Response::HTTP_CREATED,
            context: ['groups' => 'restaurant:read']
        );
    }

    /** @throws ExceptionInterface */
    #[Route('/{id}', name: 'update', methods: ['PUT', 'PATCH'])]
    public function update(
        Restaurant $restaurant,
        #[MapUpdateRequestPayload(entityClass: Restaurant::class)] UpdateRestaurantRequestDto $dto,
    ): JsonResponse
    {
        $restaurant = $this->denormalizer->denormalize(
            $dto,
            type: Restaurant::class,
            context: [
                AbstractNormalizer::OBJECT_TO_POPULATE => $restaurant,
                'groups' => 'restaurant:write',
            ],
        );

        $this->em->persist($restaurant);
        $this->em->flush();

        return $this->json(
            $restaurant,
            status: Response::HTTP_OK,
            context: ['groups' => 'restaurant:read']
        );
    }

    #[Route('/{id}', name: 'delete', methods: ['DELETE'])]
    public function delete(Restaurant $restaurant): JsonResponse
    {
        $this->em->remove($restaurant);
        $this->em->flush();

        return $this->json(null, status: Response::HTTP_NO_CONTENT);
    }
}
