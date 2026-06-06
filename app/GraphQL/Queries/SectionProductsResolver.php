<?php

namespace App\GraphQL\Queries;

use App\GraphQL\Services\HomeScreenService;
use GraphQL\Error\Error;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Resolves the `sectionProducts` query — the "View More" handler.
 *
 * Each home screen section (except Quick Buy) calls this query with a
 * SectionType enum value and pagination arguments. The mobile app navigates
 * to a dedicated listing page that calls this query as the user scrolls.
 */
class SectionProductsResolver
{
    public function __construct(
        private readonly HomeScreenService $service,
    ) {}

    public function __invoke(
        mixed $root,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ): array {
        $userId     = auth('api')->id();
        $section    = $args['section'];
        $first      = max(1, (int) ($args['first'] ?? 10));
        $page       = max(1, (int) ($args['page'] ?? 1));
        $categoryId = isset($args['categoryId']) ? (int) $args['categoryId'] : null;

        $result = match ($section) {
            'BEST_SELLING'             => $this->service->getBestSelling($userId, $first, $page),
            'TRENDING'                 => $this->service->getTrending($userId, $first, $page),
            'NEW_NOTEWORTHY'           => $this->service->getNewNoteworthy($userId, $first, $page),
            'ACCESSORIES'              => $this->service->getAccessories($userId, $first, $page),
            'RECENTLY_VIEWED'          => $this->service->getRecentlyViewed($userId, $first, $page),
            'BUDGET_CATEGORIES_PRODUCTS' => $this->service->getBudgetCategoryProducts($userId, $first, $page),
            'CATEGORY_PRODUCTS'        => $this->resolveCategoryProducts($userId, $first, $page, $categoryId),
            default                    => throw new Error(
                "Unknown section type: {$section}",
                extensions: ['code' => 422],
            ),
        };

        $count = count($result['data']);

        return [
            'data'          => $result['data'],
            'paginatorInfo' => $this->service->buildPaginatorInfo(
                $result['total'],
                $first,
                $page,
                $count,
            ),
        ];
    }

    private function resolveCategoryProducts(int $userId, int $first, int $page, ?int $categoryId): array
    {
        if (! $categoryId) {
            throw new Error(
                'categoryId is required when section is CATEGORY_PRODUCTS',
                extensions: ['code' => 422],
            );
        }

        return $this->service->getCategoryProducts($userId, $categoryId, $first, $page);
    }
}
