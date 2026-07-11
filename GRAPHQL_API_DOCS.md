# NileShopping Mobile App – GraphQL API Documentation

> **Version**: 1.0.0 · **Updated**: May 2026 · **Stack**: Laravel 12, Lighthouse GraphQL v6.54, JWT Auth

---

## Table of Contents

1. [API Overview](#1-api-overview)
2. [Authentication](#2-authentication)
3. [Home Screen API](#3-home-screen-api)
4. [Section-wise Documentation](#4-section-wise-documentation)
5. [View More Flow](#5-view-more-flow)
6. [Banner Documentation](#6-banner-documentation)
7. [Mobile Optimization](#7-mobile-optimization)
8. [Error Handling](#8-error-handling)
9. [GraphQL Quick Reference](#9-graphql-quick-reference)

---

## 1. API Overview

### Purpose

The GraphQL API powers the NileShopping mobile home screen and provides a unified, efficient way to fetch all homepage data in a **single HTTP round-trip** instead of 7+ separate REST calls.

### Endpoints

| Type | Method | URL | Auth |
|---|---|---|---|
| **GraphQL (all home screen)** | `POST` | `/api/graphql` | Required |
| Product Detail (REST) | `GET` | `/api/product/detail` | Required |
| Category List (REST) | `GET` | `/api/category/list` | Required |
| Auth – Phone Login | `POST` | `/api/login` | None |
| Auth – Email Login | `POST` | `/api/signin` | None |

> **Base URL (local dev)**: `http://localhost/nileShopping/public`
> **Base URL (production)**: Replace with your production domain.

### Headers (all authenticated requests)

```http
Authorization: Bearer <JWT_TOKEN>
Content-Type: application/json
Accept: application/json
```

---

## 2. Authentication

### Flow

```
1. POST /api/login   { "phone": "9999999999" }
   → Response: { "token": "eyJ..." }

2. Store JWT in secure local storage (e.g. Flutter_secure_storage).

3. Attach to every request:
   Authorization: Bearer eyJ...

4. Refresh before expiry:
   POST /api/token-refresh
   → New token auto-replaces the old one.
```

### Token Expiry

JWTs expire per server configuration (default: 60 minutes). Use `POST /api/token-refresh` to renew without re-logging in.

### Guest Users

| Feature | Guest | Authenticated |
|---|---|---|
| `homeScreen` query | ❌ Returns auth error | ✅ Full response |
| `sectionProducts` | ❌ | ✅ |
| `quickBuyProducts` | ❌ | ✅ |
| `recentlyViewedProducts` | ❌ (requires auth) | ✅ Personal history |
| `isFavorite` field | N/A | ✅ Real wishlist state |
| `cartId` field | N/A | ✅ Real cart state |

> **Recently Viewed for Guests**: We do **not** track views for unauthenticated users. If guest support is needed in the future, store product IDs client-side and pass them as a filter argument.

---

## 3. Home Screen API

### The `homeScreen` Query

**Purpose**: Deliver the complete home screen in one call on app launch.

**Endpoint**: `POST /api/graphql`

**Request**:
```json
{
  "query": "{ homeScreen { ... } }"
}
```

### Response Structure

```json
{
  "data": {
    "homeScreen": {
      "bannerSection":          { "ctaLabel": "...", "ctaAction": "...", "banners": [...] },
      "gendersAndCategories":   [ { "id": "1", "name": "Women", "subCategories": [...] } ],
      "bestSellingProducts":    [ { "productId": "3", "productName": "...", ... } ],
      "trendingProducts":       [...],
      "newAndNoteworthy":       [...],
      "accessoriesProducts":    [...],
      "recentlyViewedProducts": [...],
      "budgetCategories":       [ { "id": "5", "name": "...", "minPrice": 99, "maxPrice": 499 } ],
      "quickBuySection": {
        "data":          [...],
        "paginatorInfo": { "total": 45, "currentPage": 1, "lastPage": 5, "hasMorePages": true }
      }
    }
  }
}
```

---

## 4. Section-wise Documentation

### 4.1 Genders & Categories

**Field**: `gendersAndCategories`
**Returns**: Top-level gender menus (Women / Men / Kids / All) with sub-categories.

```graphql
gendersAndCategories {
  id
  name      # "Women"
  slug      # "women"
  image     # absolute URL or null
  subCategories {
    id
    name
    slug
    image
    productCount  # active products in this sub-category
  }
}
```

**Use case**: Render the gender tab bar + category chips at the top of the home screen.

---

### 4.2 Best Selling Products

**Field**: `bestSellingProducts` (in homeScreen) / **Query**: `sectionProducts(section: BEST_SELLING, ...)`
**Logic**: Ordered by the count of completed cart checkouts (carts where `status = 2`).
**Default limit**: 10 items on home screen.

```graphql
bestSellingProducts {
  productId       # listingdatas.id
  listingId       # listings.id
  productName
  sku
  mrp             # original price
  sellingPrice    # discounted price
  actualPrice     # same as mrp (convenience field)
  discount        # discount amount (future: calculated)
  rating          # avg star rating (1 decimal)
  isFavorite      # true if in user's wishlist
  cartId          # carts.id if in cart (string ID), null if not
  images          # array of absolute image URLs
  categoryName
  brandName
  hastags         # comma-separated hashtags
  seller {
    sellerName
    image
    distance      # placeholder: 0.0 (future: geo-distance)
    rating        # placeholder: 0.0 (future: seller rating)
  }
}
```

---

### 4.3 Trending Products

**Field**: `trendingProducts` / **Section enum**: `TRENDING`
**Logic**: Ordered by wishlist addition count descending, then by recency.

---

### 4.4 New & Noteworthy

**Field**: `newAndNoteworthy` / **Section enum**: `NEW_NOTEWORTHY`
**Logic**: Latest 10 active listings ordered by `listings.created_at` descending.

---

### 4.5 Accessories Products

**Field**: `accessoriesProducts` / **Section enum**: `ACCESSORIES`
**Logic**: Products from categories where `name LIKE '%accessor%' OR slug LIKE '%accessor%'`.

---

### 4.6 Recently Viewed Products

**Field**: `recentlyViewedProducts` / **Section enum**: `RECENTLY_VIEWED`
**Logic**: Products the authenticated user opened via `GET /api/product/detail`, ordered by most-recently-viewed. Stored in the `recently_viewed` table.

**Auto-tracking**: No separate tracking API call needed. The product detail REST endpoint automatically upserts a row.

---

### 4.7 Budget Categories

**Field**: `budgetCategories`
**Logic**: 8 categories that contain at least one product priced between ₹99 and ₹500.

```graphql
budgetCategories {
  id
  name
  slug
  image
  minPrice      # lowest selling_price in budget range
  maxPrice      # highest selling_price in budget range
  productCount  # products in ₹99–₹500 range
}
```

**View More**: Use `sectionProducts(section: BUDGET_CATEGORIES_PRODUCTS, ...)` to list all budget-range products.

---

### 4.8 Quick Buy Section

**Field**: `quickBuySection` / **Query**: `quickBuyProducts(...)`
**Logic**: In-stock products with lowest delivery charge first.

```graphql
quickBuySection {
  data { ... }
  paginatorInfo {
    total
    currentPage
    lastPage
    perPage
    hasMorePages
  }
}
```

**Special behaviour**: This is the **only** section that loads more items **in-place** on the home screen (no navigation). See [View More Flow](#5-view-more-flow).

---

## 5. View More Flow

### Overview

| Section | View More Behaviour | Query to use |
|---|---|---|
| Best Selling | Navigate to dedicated listing page | `sectionProducts(section: BEST_SELLING, ...)` |
| Trending | Navigate to listing page | `sectionProducts(section: TRENDING, ...)` |
| New & Noteworthy | Navigate to listing page | `sectionProducts(section: NEW_NOTEWORTHY, ...)` |
| Accessories | Navigate to listing page | `sectionProducts(section: ACCESSORIES, ...)` |
| Recently Viewed | Navigate to listing page | `sectionProducts(section: RECENTLY_VIEWED, ...)` |
| Budget Products | Navigate to listing page | `sectionProducts(section: BUDGET_CATEGORIES_PRODUCTS, ...)` |
| Category Products | Navigate to listing page | `sectionProducts(section: CATEGORY_PRODUCTS, categoryId: X, ...)` |
| **Quick Buy** | **Load more IN-PLACE** | `quickBuyProducts(first: 10, page: N)` |

### sectionProducts Query (View More)

```graphql
query ViewMore($section: SectionType!, $first: Int!, $page: Int!, $categoryId: Int) {
  sectionProducts(
    section: $section
    first: $first
    page: $page
    categoryId: $categoryId
  ) {
    data {
      productId
      productName
      sellingPrice
      rating
      isFavorite
      images
    }
    paginatorInfo {
      total
      currentPage
      lastPage
      perPage
      hasMorePages
    }
  }
}
```

**Variables**:
```json
{
  "section": "BEST_SELLING",
  "first": 10,
  "page": 1
}
```

### Pagination Pattern (standard View More page)

```
Page 1: page=1  → renders initial list
Page 2: page=2  → appended when user scrolls to bottom or taps "Load More"
Stop showing button when: paginatorInfo.hasMorePages === false
```

### Quick Buy – In-place Load More

```graphql
query QuickBuyLoadMore($page: Int!) {
  quickBuyProducts(first: 10, page: $page) {
    data { productId productName sellingPrice images }
    paginatorInfo { hasMorePages currentPage total }
  }
}
```

**Flutter implementation**:
```dart
int _quickBuyPage = 1;
bool _hasMore = true;
List<Product> _products = [];

Future<void> loadMoreQuickBuy() async {
  if (!_hasMore) return;
  final result = await client.query(QuickBuyOptions(page: _quickBuyPage));
  final pageData = result.data['quickBuyProducts'];
  setState(() {
    _products.addAll(parseProducts(pageData['data']));
    _hasMore = pageData['paginatorInfo']['hasMorePages'];
    _quickBuyPage++;
  });
}
```

**React Native implementation**:
```js
const [page, setPage] = useState(1);
const [hasMore, setHasMore] = useState(true);
const [products, setProducts] = useState([]);

const loadMore = async () => {
  if (!hasMore) return;
  const { data } = await apolloClient.query({
    query: QUICK_BUY_QUERY,
    variables: { first: 10, page },
  });
  const info = data.quickBuyProducts.paginatorInfo;
  setProducts(prev => [...prev, ...data.quickBuyProducts.data]);
  setHasMore(info.hasMorePages);
  setPage(prev => prev + 1);
};
```

---

## 6. Banner Documentation

### Banner Structure

```graphql
type Banner {
  id:            ID!
  image:         String!      # absolute URL to banner image
  title:         String       # optional display text
  redirectType:  String!      # "url" | "category" | "product"
  redirectValue: String!      # depends on redirectType (see below)
  displayOrder:  Int!         # ascending sort order (1, 2, 3...)
  isActive:      Boolean!     # only true banners are returned
}
```

### Redirect Types

| `redirectType` | `redirectValue` | Client action |
|---|---|---|
| `url` | Full path e.g. `/offers/season-sale` | `Navigator.push(url)` |
| `category` | Category slug e.g. `women` | Navigate to category listing screen |
| `product` | Product ID e.g. `42` | Navigate to product detail screen |

### Client-side Redirect Handler

```dart
// Flutter
void handleBannerTap(Banner banner) {
  switch (banner.redirectType) {
    case 'url':
      Navigator.pushNamed(context, banner.redirectValue);
      break;
    case 'category':
      Navigator.push(context, CategoryScreen(slug: banner.redirectValue));
      break;
    case 'product':
      Navigator.push(context, ProductScreen(id: banner.redirectValue));
      break;
  }
}
```

### CTA Button

The `bannerSection.ctaLabel` ("View Offers") and `bannerSection.ctaAction` ("VIEW_OFFERS") are intended for the button displayed **above** the banner carousel. Tapping it can navigate to a dedicated offers/deals screen.

### Display Order Logic

Banners are returned sorted by `display_order` ascending. Always render them in the order returned — do not sort client-side.

### Admin Management

Banners are managed via the `banners` database table. An admin panel UI for banners can be added to the Laravel admin in a future sprint.

---

## 7. Mobile Optimization

### Recommended Query Usage

| Scenario | Recommended Approach |
|---|---|
| App launch / home screen mount | Call `homeScreen` once — all sections in one request |
| User taps "View More" on any section | Call `sectionProducts` with appropriate `section` enum |
| User taps "Load More" on Quick Buy | Call `quickBuyProducts` with incremented `page` |
| Banner-only refresh | Use focused banner query (subset of `homeScreen`) |
| Pull-to-refresh | Re-call `homeScreen` with same query |

### Minimize Payload

Only request fields you actually render. Example — if your product card doesn't show seller info, omit the `seller` fragment:

```graphql
# Lightweight card query (omit unused fields)
bestSellingProducts {
  productId
  productName
  sellingPrice
  rating
  images
  isFavorite
}
```

### Lazy Loading Images

- `images` is an array. Use only `images[0]` for card thumbnails.
- Load additional images (carousel) only when the user opens product detail.

### Caching Recommendations

| Section | Client cache TTL | Strategy |
|---|---|---|
| `bannerSection` | 10 minutes | Stale-while-revalidate |
| `gendersAndCategories` | 30 minutes | Cache + background refresh |
| `bestSellingProducts` | 5 minutes | Time-based invalidation |
| `trendingProducts` | 5 minutes | Time-based invalidation |
| `newAndNoteworthy` | 2 minutes | Frequent refresh |
| `recentlyViewedProducts` | 0 (always fresh) | No cache |
| `quickBuySection` | 5 minutes | Invalidate on cart change |

---

## 8. Error Handling

### Standard GraphQL Error Shape

```json
{
  "errors": [
    {
      "message": "Human-readable error message",
      "locations": [{ "line": 1, "column": 3 }],
      "path": ["homeScreen"],
      "extensions": {
        "category": "authentication|validation|internal"
      }
    }
  ]
}
```

### Authentication Error (401)

```json
{
  "errors": [{
    "message": "Unauthenticated.",
    "extensions": { "category": "authentication" }
  }]
}
```
**Client action**: Redirect user to login screen. Attempt token refresh first if a refresh token is available.

### Validation Error (422)

```json
{
  "errors": [{
    "message": "Unknown section type: INVALID_SECTION",
    "extensions": { "code": 422 }
  }]
}
```
**Client action**: Log to crash reporting. Do not show raw error to user.

### Empty State Handling

GraphQL queries always return HTTP 200. Empty arrays `[]` indicate no data for a section — **not** an error state. Render an empty state UI (e.g. "No products found") for empty arrays.

```json
{
  "data": {
    "homeScreen": {
      "recentlyViewedProducts": []
    }
  }
}
```

| Section | Empty state message |
|---|---|
| `recentlyViewedProducts` | "You haven't viewed any products yet" |
| `accessoriesProducts` | "No accessories available right now" |
| `budgetCategories` | "No budget deals available right now" |

---

## 9. GraphQL Quick Reference

### All Queries

#### `homeScreen` — Full Home Screen
No arguments. Returns all sections.

#### `sectionProducts` — View More (Paginated)
```
section:    SectionType!  (required)
first:      Int!          (default: 10)
page:       Int!          (default: 1)
categoryId: Int           (required only for CATEGORY_PRODUCTS)
```

#### `quickBuyProducts` — Quick Buy In-Place Pagination
```
first: Int!  (default: 10)
page:  Int!  (default: 1)
```

### SectionType Enum Values

| Value | Description |
|---|---|
| `BEST_SELLING` | Top sellers by completed orders |
| `TRENDING` | Most-wishlisted + recent |
| `NEW_NOTEWORTHY` | Newest listings |
| `ACCESSORIES` | Accessory category products |
| `RECENTLY_VIEWED` | User's recent product views |
| `BUDGET_CATEGORIES_PRODUCTS` | All products priced ₹99–₹500 |
| `CATEGORY_PRODUCTS` | Products by `categoryId` (menus.id) |

### Product Fields Reference

| Field | Type | Description |
|---|---|---|
| `productId` | `ID!` | `listingdatas.id` |
| `listingId` | `ID!` | `listings.id` |
| `productName` | `String!` | Display name |
| `sku` | `String` | Stock Keeping Unit |
| `mrp` | `Float!` | Maximum Retail Price |
| `sellingPrice` | `Float!` | Current selling price |
| `actualPrice` | `Float!` | Same as `mrp` (convenience) |
| `discount` | `Float!` | Discount amount (0.0 currently) |
| `rating` | `Float!` | Avg rating (0.0–5.0, 1 decimal) |
| `isFavorite` | `Boolean!` | In user's wishlist |
| `cartId` | `ID` | Cart row ID, null if not in cart |
| `images` | `[String!]!` | Absolute image URLs |
| `categoryName` | `String` | Category display name |
| `brandName` | `String` | Brand display name |
| `hastags` | `String` | Comma-separated hashtags |
| `seller.sellerName` | `String!` | Seller display name |
| `seller.image` | `String!` | Seller avatar URL |
| `seller.distance` | `Float!` | Placeholder 0.0 |
| `seller.rating` | `Float!` | Placeholder 0.0 |

### PaginatorInfo Fields Reference

| Field | Type | Description |
|---|---|---|
| `total` | `Int!` | Total items across all pages |
| `count` | `Int!` | Items on current page |
| `currentPage` | `Int!` | Current page (1-based) |
| `lastPage` | `Int!` | Last available page |
| `perPage` | `Int!` | Items per page |
| `firstItem` | `Int` | Index of first item on page |
| `lastItem` | `Int` | Index of last item on page |
| `hasMorePages` | `Boolean!` | Whether to show Load More |

---

## Setup Commands (for backend team)

```bash
# Run after composer install
php artisan migrate                                # creates recently_viewed + banners tables
php artisan db:seed --class=BannerSeeder          # seeds 3 sample banners
php artisan config:clear && php artisan route:clear
php artisan route:list | findstr graphql           # should show POST api/graphql
```

---

*Documentation generated from `graphql/schema.graphql` and `app/GraphQL/` · NileShopping v2*
