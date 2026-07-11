<?php

namespace App\GraphQL\Services;

use Illuminate\Support\Facades\DB;

/**
 * HomeScreenService
 *
 * All methods use batch queries (no N+1 loops). Every public section getter
 * returns ['data' => Product[], 'total' => int] so callers can build both the
 * homeScreen snapshot and the paginated View-More response from the same logic.
 */
class HomeScreenService
{
    // ─────────────────────────────────────────────────────────────────
    // Banners
    // ─────────────────────────────────────────────────────────────────

    public function getBanners(): array
    {
        $rows = DB::table('banners')
            ->where('is_active', true)
            ->orderBy('display_order')
            ->limit(3)
            ->get();

        $banners = [];
        foreach ($rows as $row) {
            $banners[] = [
                'id'            => $row->id,
                'image'         => url('/') . '/uploads/banners/' . $row->image,
                'title'         => $row->title,
                'redirectType'  => $row->redirect_type,
                'redirectValue' => $row->redirect_value,
                'displayOrder'  => $row->display_order,
                'isActive'      => (bool) $row->is_active,
            ];
        }

        return $banners;
    }

    // ─────────────────────────────────────────────────────────────────
    // Genders & Categories
    // ─────────────────────────────────────────────────────────────────

    /**
     * Returns top-level gender menus (Women, Men, Kids, All) with their
     * sub-categories and per-sub-category product counts.
     * Uses 2 queries total (not N per category).
     */
    public function getGendersAndCategories(): array
    {
        // 1. Fetch all top-level genders (menu_id=2, parent_id IS NULL)
        $parents = DB::table('menus')
            ->where('menu_id', 2)
            ->where('slug', 'dropdown')
            ->whereNull('parent_id')
            ->orderBy('sequence')
            ->get();

        if ($parents->isEmpty()) {
            return [];
        }

        $parentIds = $parents->pluck('id')->toArray();

        // 2. Fetch all sub-categories for those parents in one query
        $children = DB::table('menus')
            ->where('menu_id', 2)
            ->whereIn('parent_id', $parentIds)
            ->orderBy('sequence')
            ->get()
            ->groupBy('parent_id');

        // 3. Count active products per sub-category in one query
        $productCounts = DB::table('listings')
            ->select('menu_id', DB::raw('COUNT(*) as cnt'))
            ->where('status', 3)
            ->whereNull('deleted_at')
            ->whereIn('menu_id', $children->flatten()->pluck('id')->toArray())
            ->groupBy('menu_id')
            ->get()
            ->keyBy('menu_id');

        $result = [];
        foreach ($parents as $parent) {
            $subs = [];
            $subList = $children->get($parent->id, collect());
            foreach ($subList as $child) {
                $subs[] = [
                    'id'           => $child->id,
                    'name'         => $child->name,
                    'slug'         => $child->slug,
                    'image'        => $child->image
                        ? url('/') . '/uploads/menu-icon/' . $child->image
                        : null,
                    'productCount' => (int) ($productCounts->get($child->id)->cnt ?? 0),
                ];
            }

            $result[] = [
                'id'            => $parent->id,
                'name'          => $parent->name,
                'slug'          => $parent->slug,
                'image'         => $parent->image
                    ? url('/') . '/uploads/menu-icon/' . $parent->image
                    : null,
                'subCategories' => $subs,
            ];
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────
    // Product section getters
    // ─────────────────────────────────────────────────────────────────

    /**
     * Best Selling: ordered by number of completed cart checkouts (status=2).
     */
    public function getBestSelling(int $userId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at');
            })
            ->leftJoin('carts as c', function ($j) {
                $j->on('c.product_id', '=', 'ld.id')
                  ->where('c.status', 2)
                  ->whereNull('c.deleted_at');
            })
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->select('l.*', DB::raw('COUNT(c.id) as order_count'))
            ->groupBy('l.id')
            ->orderByDesc('order_count')
            ->orderByDesc('l.created_at');

        $total = DB::table(DB::raw("({$base->toSql()}) as sub"))
            ->mergeBindings($base)
            ->count();

        $rows = (clone $base)->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    /**
     * Trending: ordered by wishlist additions + recency.
     */
    public function getTrending(int $userId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at');
            })
            ->leftJoin('whishlist as w', function ($j) {
                $j->on('w.listing_id', '=', 'l.id')
                  ->whereNull('w.deleted_at');
            })
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->select('l.*', DB::raw('COUNT(w.id) as wish_count'))
            ->groupBy('l.id')
            ->orderByDesc('wish_count')
            ->orderByDesc('l.created_at');

        $total = DB::table(DB::raw("({$base->toSql()}) as sub"))
            ->mergeBindings($base)
            ->count();

        $rows = (clone $base)->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    /**
     * New & Noteworthy: latest listings by created_at.
     */
    public function getNewNoteworthy(int $userId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at');
            })
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->select('l.*')
            ->orderByDesc('l.created_at');

        $total = $base->clone()->count();
        $rows  = $base->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    /**
     * Accessories: products from categories whose name/slug contains 'accessor'.
     */
    public function getAccessories(int $userId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at');
            })
            ->join('categories as cat', 'cat.id', '=', 'l.category_id')
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->where(function ($q) {
                $q->where('cat.name', 'like', '%accessor%')
                  ->orWhere('cat.slug', 'like', '%accessor%');
            })
            ->select('l.*')
            ->orderByDesc('l.created_at');

        $total = $base->clone()->count();
        $rows  = $base->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    /**
     * Recently Viewed: products the authenticated user viewed last.
     */
    public function getRecentlyViewed(int $userId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at');
            })
            ->join('recently_viewed as rv', function ($j) use ($userId) {
                $j->on('rv.listing_id', '=', 'l.id')
                  ->where('rv.user_id', $userId);
            })
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->select('l.*')
            ->orderByDesc('rv.updated_at');

        $total = $base->clone()->count();
        $rows  = $base->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    /**
     * Budget categories: categories containing products priced ₹99–₹500.
     */
    public function getBudgetCategories(int $limit = 8): array
    {
        $rows = DB::table('categories as cat')
            ->join('listings as l', function ($j) {
                $j->on('l.category_id', '=', 'cat.id')
                  ->where('l.status', 3)
                  ->whereNull('l.deleted_at');
            })
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at')
                  ->whereBetween('ld.selling_price', [99, 500]);
            })
            ->select(
                'cat.id',
                'cat.name',
                'cat.slug',
                'cat.image',
                DB::raw('MIN(ld.selling_price) as min_price'),
                DB::raw('MAX(ld.selling_price) as max_price'),
                DB::raw('COUNT(DISTINCT l.id) as product_count')
            )
            ->groupBy('cat.id', 'cat.name', 'cat.slug', 'cat.image')
            ->having('product_count', '>', 0)
            ->orderByDesc('product_count')
            ->limit($limit)
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $result[] = [
                'id'           => $row->id,
                'name'         => $row->name,
                'slug'         => $row->slug,
                'image'        => $row->image
                    ? url('/') . '/uploads/categories/' . $row->image
                    : null,
                'minPrice'     => (float) $row->min_price,
                'maxPrice'     => (float) $row->max_price,
                'productCount' => (int) $row->product_count,
            ];
        }

        return $result;
    }

    /**
     * Budget category products: products priced ₹99–₹500 (for View More).
     */
    public function getBudgetCategoryProducts(int $userId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at')
                  ->whereBetween('ld.selling_price', [99, 500]);
            })
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->select('l.*')
            ->orderByDesc('l.created_at');

        $total = $base->clone()->count();
        $rows  = $base->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    /**
     * Category products: products filtered by a specific menu/category ID.
     */
    public function getCategoryProducts(int $userId, int $categoryId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at');
            })
            ->whereIn('l.menu_id', function ($q) use ($categoryId) {
                $q->select('id')
                  ->from('menus')
                  ->where('id', $categoryId)
                  ->orWhere('parent_id', $categoryId);
            })
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->select('l.*')
            ->orderByDesc('l.created_at');

        $total = $base->clone()->count();
        $rows  = $base->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    /**
     * Quick Buy: in-stock products with lowest delivery charges for fast purchase.
     */
    public function getQuickBuy(int $userId, int $limit = 10, int $page = 1): array
    {
        $offset = ($page - 1) * $limit;

        $base = DB::table('listings as l')
            ->join('listingdatas as ld', function ($j) {
                $j->on('ld.listing_id', '=', 'l.id')
                  ->where('ld.status', 1)
                  ->whereNull('ld.deleted_at');
            })
            ->where('l.status', 3)
            ->whereNull('l.deleted_at')
            ->where(function ($q) {
                // Products with stock available (stock field is varchar)
                $q->whereNotNull('ld.stock')
                  ->where('ld.stock', '!=', '')
                  ->where('ld.stock', '!=', '0');
            })
            ->select('l.*', DB::raw('CAST(ld.local_delivery_charge AS DECIMAL(10,2)) as delivery_charge_num'))
            ->orderBy('delivery_charge_num')
            ->orderByDesc('l.created_at');

        $total = $base->clone()->count();
        $rows  = $base->offset($offset)->limit($limit)->get()->toArray();

        return [
            'data'  => $this->buildProductPayload($rows, $userId),
            'total' => $total,
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    // Shared batch payload builder  (N+1 free)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Given an array of `listings` rows, build the full Product payload
     * for each in a fixed number of queries (independent of how many listings).
     *
     * @param  \stdClass[]  $listings
     * @param  int          $userId   authenticated user's ID
     * @return array<int, array>
     */
    public function buildProductPayload(array $listings, int $userId): array
    {
        if (empty($listings)) {
            return [];
        }

        $listingIds  = array_column($listings, 'id');
        $categoryIds = array_unique(array_column($listings, 'category_id'));
        $brandIds    = array_unique(array_column($listings, 'brand_id'));
        $sellerIds   = array_unique(array_column($listings, 'user_id'));

        // ── Batch 1: listingdatas (one active record per listing) ──────
        $listingdataMap = DB::table('listingdatas')
            ->whereIn('listing_id', $listingIds)
            ->where('status', 1)
            ->whereNull('deleted_at')
            ->get()
            ->keyBy('listing_id');

        if ($listingdataMap->isEmpty()) {
            return [];
        }

        $listingdataIds = $listingdataMap->pluck('id')->toArray();

        // ── Batch 2: listing photos (grouped per listing) ──────────────
        $photosMap = DB::table('listingphotos')
            ->whereIn('listing_id', $listingIds)
            ->whereNull('deleted_at')
            ->get()
            ->groupBy('listing_id');

        // ── Batch 3: categories ────────────────────────────────────────
        $categoryMap = DB::table('categories')
            ->whereIn('id', $categoryIds)
            ->get()
            ->keyBy('id');

        // ── Batch 4: brands ────────────────────────────────────────────
        $brandMap = DB::table('brands')
            ->whereIn('brand_id', $brandIds)
            ->get()
            ->keyBy('brand_id');

        // ── Batch 5: sellers (users) ───────────────────────────────────
        $sellerMap = DB::table('users')
            ->whereIn('id', $sellerIds)
            ->select('id', 'name', 'image')
            ->get()
            ->keyBy('id');

        // ── Batch 6: average ratings per listingdata ───────────────────
        $ratingMap = DB::table('reviews')
            ->whereIn('product_id', $listingdataIds)
            ->whereNull('deleted_at')
            ->select('product_id', DB::raw('AVG(rating) as avg_rating'))
            ->groupBy('product_id')
            ->get()
            ->keyBy('product_id');

        // ── Batch 7: wishlist (listing_id set for the current user) ────
        $wishlistSet = DB::table('whishlist')
            ->where('user_id', $userId)
            ->whereIn('listing_id', $listingIds)
            ->whereNull('deleted_at')
            ->pluck('listing_id')
            ->flip(); // collection keyed by listing_id for O(1) lookup

        // ── Batch 8: active cart items for the current user ────────────
        $cartMap = DB::table('carts')
            ->where('user_id', $userId)
            ->whereIn('product_id', $listingdataIds)
            ->whereNull('deleted_at')
            ->select('product_id', 'id')
            ->get()
            ->keyBy('product_id');

        // ── Compose results ────────────────────────────────────────────
        $result = [];
        foreach ($listings as $listing) {
            /** @var \stdClass $ld */
            $ld = $listingdataMap->get($listing->id);
            if (! $ld) {
                continue; // no active listingdata → skip
            }

            // Images
            $images  = [];
            $photos  = $photosMap->get($listing->id, collect());
            foreach ($photos as $photo) {
                foreach (['image_1', 'image_2', 'image_3', 'image_4', 'image_5'] as $field) {
                    if (! empty($photo->$field)) {
                        $images[] = url('/') . '/uploads/listings/' . $listing->id . '/' . $photo->$field;
                    }
                }
            }

            $category  = $categoryMap->get($listing->category_id);
            $brand     = $brandMap->get($listing->brand_id);
            $seller    = $sellerMap->get($listing->user_id);
            $ratingRow = $ratingMap->get($ld->id);
            $avgRating = $ratingRow
                ? round((float) $ratingRow->avg_rating, 1)
                : 0.0;

            $result[] = [
                'productId'    => (string) $ld->id,
                'listingId'    => (string) $listing->id,
                'productName'  => (string) ($ld->product_name ?? ''),
                'sku'          => $ld->sku,
                'mrp'          => (float) ($ld->mrp ?? 0),
                'sellingPrice' => (float) ($ld->selling_price ?? 0),
                'actualPrice'  => (float) ($ld->mrp ?? 0),
                'discount'     => 0.00,
                'rating'       => $avgRating,
                'isFavorite'   => $wishlistSet->has($listing->id),
                'cartId'       => $cartMap->has($ld->id)
                    ? (string) $cartMap->get($ld->id)->id
                    : null,
                'images'       => $images,
                'categoryName' => $category?->name ?? null,
                'brandName'    => $brand?->brand_name ?? null,
                'hastags'      => $listing->hastags ?? null,
                'seller'       => [
                    'sellerName' => $seller?->name ?? '',
                    'image'      => ($seller?->image)
                        ? url('/') . '/uploads/profile/' . $seller->image
                        : '',
                    'distance'   => 0.0,
                    'rating'     => 0.0,
                ],
            ];
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────
    // Pagination helper
    // ─────────────────────────────────────────────────────────────────

    /**
     * Build the PaginatorInfo array from raw totals.
     */
    public function buildPaginatorInfo(int $total, int $perPage, int $currentPage, int $count): array
    {
        $lastPage = max(1, (int) ceil($total / $perPage));

        return [
            'total'        => $total,
            'count'        => $count,
            'currentPage'  => $currentPage,
            'lastPage'     => $lastPage,
            'perPage'      => $perPage,
            'firstItem'    => $count > 0 ? ($currentPage - 1) * $perPage + 1 : null,
            'lastItem'     => $count > 0 ? ($currentPage - 1) * $perPage + $count : null,
            'hasMorePages' => $currentPage < $lastPage,
        ];
    }
}
