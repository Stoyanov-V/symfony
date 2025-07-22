<?php

declare(strict_types=1);

namespace App\Controller\Api\V1;

use App\Dto\V1\Restaurants\GetRestaurantsQueryDto;
use App\Entity\Restaurant;
use App\Filters\V1\RestaurantFilter;
use App\Repository\RestaurantRepository;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/v1/restaurants', name: 'api_v1_restaurants_', format: 'json', stateless: true)]
final class RestaurantsController extends AbstractController
{
    /**
     * @throws Exception
     */
    #[Route('/', name: 'index')]
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
}
