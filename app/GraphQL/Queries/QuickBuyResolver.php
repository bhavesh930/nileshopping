<?php

namespace App\GraphQL\Queries;

use App\GraphQL\Services\HomeScreenService;
use GraphQL\Type\Definition\ResolveInfo;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

/**
 * Resolves the `quickBuyProducts` query.
 *
 * Unlike other sections, Quick Buy loads MORE products IN-PLACE on the
 * home screen (no navigation to a separate page). The mobile app calls
 * this query with incrementing page numbers as the user taps "View More".
 */
class QuickBuyResolver
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
        $userId = auth('api')->id();
        $first  = max(1, (int) ($args['first'] ?? 10));
        $page   = max(1, (int) ($args['page'] ?? 1));

        $result = $this->service->getQuickBuy($userId, $first, $page);
        $count  = count($result['data']);

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
}
