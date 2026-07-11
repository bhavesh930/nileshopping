<?php

namespace App\GraphQL\Queries;

use App\GraphQL\Services\HomeScreenService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Resolves the `homeScreen` query.
 *
 * Returns all sections in a single response so the mobile app can render
 * the entire home screen from one HTTP round-trip.
 */
class HomeScreenResolver
{
    public function __construct(
        private readonly HomeScreenService $service,
    ) {}

    /**
     * @param  null       $root
     * @param  array      $args
     * @param  GraphQLContext $context
     * @param  ResolveInfo   $resolveInfo
     * @return array
     */
    public function __invoke(
        mixed $root,
        array $args,
        GraphQLContext $context,
        ResolveInfo $resolveInfo,
    ): array {
        $userId = auth('api')->id();

        // ── Get quick-buy first 10 with pagination info ────────────────
        $quickBuyResult = $this->service->getQuickBuy($userId, 10, 1);
        $quickBuyCount  = count($quickBuyResult['data']);

        return [
            // Banner section ─────────────────────────────────────────────
            'bannerSection' => [
                'ctaLabel'  => 'View Offers',
                'ctaAction' => 'VIEW_OFFERS',
                'banners'   => $this->service->getBanners(),
            ],

            // Genders & categories ───────────────────────────────────────
            'gendersAndCategories' => $this->service->getGendersAndCategories(),

            // Product sections (snapshot — first 10 each) ────────────────
            'bestSellingProducts'    => $this->service->getBestSelling($userId, 10, 1)['data'],
            'trendingProducts'       => $this->service->getTrending($userId, 10, 1)['data'],
            'newAndNoteworthy'       => $this->service->getNewNoteworthy($userId, 10, 1)['data'],
            'accessoriesProducts'    => $this->service->getAccessories($userId, 10, 1)['data'],
            'recentlyViewedProducts' => $this->service->getRecentlyViewed($userId, 10, 1)['data'],

            // Budget categories ──────────────────────────────────────────
            'budgetCategories' => $this->service->getBudgetCategories(8),

            // Quick Buy with in-place pagination info ─────────────────────
            'quickBuySection' => [
                'data'          => $quickBuyResult['data'],
                'paginatorInfo' => $this->service->buildPaginatorInfo(
                    $quickBuyResult['total'],
                    10,
                    1,
                    $quickBuyCount,
                ),
            ],
        ];
    }
}
