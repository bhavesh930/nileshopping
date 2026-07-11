# NileShopping – Upgrade TODO

## Project Stack
- Laravel 8, PHP 7.2.5+ (platform: 8.2.8), CoreUI Admin Template
- Roles: `admin`, `seller`, `employee`, `user`, `guest`
- Key packages: `spatie/laravel-permission`, `tymon/jwt-auth`, `spatie/laravel-medialibrary`

---

## 1. Bugs / Broken Code

- [ ] **SellerController@store** calls `$validatedData->fails()` but `$this->validate()` throws on failure — it never returns a validator object. Remove the dead `if ($validatedData->fails())` block.
- [ ] **SellerController** uses `redirect()->action('SellerController@createListing', ...)` (deprecated in Laravel 8). Replace with `redirect()->route('createListing', [...])`.
- [ ] **StoreInventory migration** has no primary key (`id`). `StoreInventory::updateOrCreate` and `findOrFail($id)` calls in `StoreController` will fail. Add `$table->id()` to the migration.
- [ ] **`store_inventory` foreign key** for `listing_id` is commented out. Either add it or document why it's intentionally missing.
- [ ] **`StoreEmployeeController` middleware** registers `employee.permission:manage_products` on `index` and `edit` — employees should be able to view the list without that permission. Fix the `only([...])` list.
- [ ] **`EmployeeMiddleware` / `EmployeePermission`** — middleware classes exist in Kernel but need to be verified they exist at `app/Http/Middleware/EmployeeMiddleware.php` and `EmployeePermission.php`.
- [ ] **`myListingPhotoStore`** uses `unlink(url('/').'/uploads/...')` (a URL, not a filesystem path). Should be `unlink(public_path('/uploads/...'))`.
- [ ] **`Listing` model** has `$dates = ['deleted_at']` but does not `use SoftDeletes`. Either add the trait or remove the column reference.
- [ ] **Duplicate `store` table migration** — both `2023_10_24_073904_create_store_table.php` and `2026_03_29_054314_create_stores_table.php` exist. The old one likely conflicts. Verify and drop/remove the obsolete one.
- [ ] **`web.php` employee redirect** — on login, employee is redirected to `seller.stores.products` but that route requires `seller.` prefix group which has no auth middleware enforced (uses empty `Route::group([], ...)`). Add `auth` middleware to that group.

---

## 2. Missing Middleware / Auth Guards

- [ ] The `seller.` route group uses `Route::group([], ...)` — no `role:seller` or `auth` middleware. Any logged-in user can access store management routes. Add `['middleware' => ['auth', 'role:seller|employee']]`.
- [ ] `StoreController` manually checks `hasRole()` in every method instead of using middleware. Consolidate with route middleware.

---

## 3. Missing Views

- [ ] `seller/stores/show.blade.php` — referenced in `StoreController@show` ✅ exists
- [ ] `seller/store/add.blade.php` — referenced in `SellerController@sellerStoreCreate` ✅ exists  
- [ ] Verify all store views (`index`, `create`, `edit`, `products`, `employees`, `employee-create`, `employee-edit`) are complete and not empty stubs.

---

## 4. Missing / Incomplete Features

- [ ] **Order management** — `OrderController` exists and routes are defined but views `dashboard/order/detail.blade.php` and `dashboard/order/list.blade.php` need to be verified as functional.
- [ ] **Seller registration** — `SellerRegistrationController` is referenced in routes but not listed in controllers directory. Verify it exists or create it.
- [ ] **`ListingController`** — exists in `Api/` only (`ListingAuthController`). The web `ListingController` referenced in commented-out routes does not exist.
- [ ] **`sellerStoreCreate`** (`/store/create`) renders `seller.store.add` — this is the old store creation view, separate from the new `seller/stores/create`. Consolidate or remove.
- [ ] **API routes** — `routes/api.php` has JWT-auth API controllers (`CategoryAuthController`, `ListingAuthController`, `UserAuthController`). Document or complete API feature set.
- [ ] **`store_id` on listings** — migration adds `store_id` and `is_global` to `listings` table, but `Listing` model `$fillable` does not include these fields. Add them.

---

## 5. Code Quality / Refactoring

- [ ] **Repeated permission-check logic** in `StoreController` (seller vs employee check in every method). Extract to a private `authorizeStoreAccess($storeId)` helper or a Policy.
- [ ] **`Seller` model** uses raw `DB::table()` queries throughout. Migrate to Eloquent relationships on `Listing` model.
- [ ] **`storeListingData`** in `SellerController` is ~100 lines of `if(isset(...))` blocks. Refactor using `$request->only([...])` + `array_filter`.
- [ ] **`Category::isVerticalCategory`** does not handle null results (if `$id` doesn't exist, `->first()` returns null and `->parent_id` throws). Add null checks.
- [ ] **`StoreController@store`** manually builds `store_slug` with `Str::slug()` but `Store` model boot method also does this on `creating`. Remove the duplicate in the controller.
- [ ] **`Users` model vs `User` model** — both exist (`User.php` and `Users.php`). Clarify which is canonical and remove or alias the other.

---

## 6. Database / Migration Issues

- [ ] Run `php artisan migrate:status` to confirm all migrations have run cleanly, especially the 2026-dated ones.
- [ ] `store_employees` has `unique(['store_id', 'user_id'])` — an employee can only belong to one store. Confirm this is intentional (vs. an employee working at multiple stores).
- [ ] Add seeders for `stores`, `store_employees`, and `store_inventory` tables for local dev/testing.

---

## 7. Security

- [ ] `StoreEmployeeController@store` creates a user with `email_verified_at => now()` — bypasses email verification. Intentional? Document it.
- [ ] `StoreEmployeeController@destroy` deletes the user account entirely. Consider soft-delete only or deactivating (`is_active = false`) instead.
- [ ] File uploads in `StoreController` store to `public` disk without validating file type beyond MIME. Consider adding virus scan or stricter validation.
- [ ] `SellerController@store` stores plain `employeeCode` in sellers table — unclear purpose. Document or remove.

---

## 8. Next Priority Order

1. Fix `StoreInventory` missing primary key (breaks inventory CRUD)
2. Fix route group missing auth/role middleware (security)
3. Fix `SellerController@store` broken validation logic
4. Fix `myListingPhotoStore` broken `unlink` path
5. Add `store_id` / `is_global` to `Listing` `$fillable`
6. Verify `SellerRegistrationController` exists
7. Refactor `StoreController` permission checks into a Policy
8. Clean up duplicate store migration
