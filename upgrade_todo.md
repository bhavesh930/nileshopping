# NileShopping – Laravel 8 → Laravel 12 + PHP 8.4 Upgrade TODO

## Current State
- Laravel 8, PHP 7.2.5 (platform locked to 8.2.8), MySQL
- Key packages: spatie/laravel-permission ^3.17, spatie/laravel-medialibrary ^8.7.2, tymon/jwt-auth ^1.0, laravel/ui ^3.0

## Target State
- Laravel 12, PHP 8.4, same DB, same Blade/CoreUI frontend

---

## Phase 1 – Git Backup

- [ ] `git checkout -b backup/laravel8-original` and commit all files
- [ ] `git checkout -b upgrade/laravel-12` to work on

---

## Phase 2 – composer.json

- [ ] Change `php` to `^8.4`
- [ ] Change `laravel/framework` to `^12.0`
- [ ] Change `laravel/tinker` to `^2.9`
- [ ] Change `laravel/ui` to `^5.0` — `^4.0` is Laravel 9-10 only, `^5.0` is Laravel 11+
- [ ] Remove `fideloper/proxy` — built into Laravel 9+
- [ ] Remove `fruitcake/laravel-cors` — built into Laravel 9+
- [ ] Replace `facade/ignition` with `spatie/laravel-ignition ^3.0` — `^2.0` is Laravel 9-10 only, `^3.0` is Laravel 11+
- [ ] Replace `fzaninotto/faker` with `fakerphp/faker ^1.9`
- [ ] Change `spatie/laravel-permission` to `^6.0`
- [ ] Change `spatie/laravel-medialibrary` to `^11.4` — `^11.4` minimum for Laravel 12 support
- [ ] Remove `tymon/jwt-auth` — replace with `php-open-source-saver/jwt-auth ^2.0` — tymon has no `^2.0` release, last release is `^1.0` and is abandoned
- [ ] Change `nunomaduro/collision` to `^8.0`
- [ ] Change `phpunit/phpunit` to `^11.0`
- [ ] Change `mockery/mockery` to `^1.6` — `^1.3` does not support PHPUnit 11 + PHP 8.4
- [ ] Update `doctrine/dbal` to `^3.8`
- [ ] Remove `config.platform.php` override (`8.2.8`) — no longer needed
- [ ] Run `composer update`

---

## Phase 3 – Middleware Files

- [ ] `TrustProxies.php` — change `use Fideloper\Proxy\TrustProxies` to `use Illuminate\Http\Middleware\TrustProxies`
- [ ] `CheckForMaintenanceMode.php` — rename file and class to `PreventRequestsDuringMaintenance`, change parent import to `Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance`
- [ ] `Authenticate.php` — add return type `string|null` to `redirectTo()` method
- [ ] `RedirectIfAuthenticated.php` — update `handle()` to support multiple guards via `...$guards` spread and add `redirectTo()` method with `string|null` return type — NOTE: this file currently has no `redirectTo()` method, the fix is not just adding a return type
- [ ] `EncryptCookies.php` — no changes needed ✅
- [ ] `VerifyCsrfToken.php` — no changes needed ✅
- [ ] `TrimStrings.php` — no changes needed ✅
- [ ] `Admin.php` — no changes needed ✅
- [ ] `GetMenu.php` — no changes needed ✅
- [ ] `EmployeeMiddleware.php` — no changes needed ✅
- [ ] `EmployeePermission.php` — add `Auth::check()` guard at top before accessing `$user->menuroles` to prevent null error on unauthenticated requests

---

## Phase 4 – app/Http/Kernel.php

> NOTE: Phase 4 is only needed if you are NOT doing the full `bootstrap/app.php` rewrite in Phase 5. If doing Phase 5, skip Phase 4 and delete `Kernel.php` instead.

- [ ] Replace `CheckForMaintenanceMode::class` with `PreventRequestsDuringMaintenance::class`
- [ ] Rename `$routeMiddleware` to `$middlewareAliases`
- [ ] Fix `'bindings'` string in api group — replace with `\Illuminate\Routing\Middleware\SubstituteBindings::class`
- [ ] Update Spatie RoleMiddleware path — in v6 namespace changed from `Middlewares` to `Middleware`:
  - Old: `\Spatie\Permission\Middlewares\RoleMiddleware::class`
  - New: `\Spatie\Permission\Middleware\RoleMiddleware::class`

---

## Phase 5 – bootstrap/app.php

- [ ] Rewrite entirely to L11/L12 fluent API — register middleware, routes, exceptions here
- [ ] Register jwt-auth service provider here: `\PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider::class` — NOT auto-discovered, must be explicit
- [ ] Register `JWTAuth` and `JWTFactory` aliases here (removed from `config/app.php` in Phase 10)
- [ ] After this, `app/Http/Kernel.php` can be deleted
- [ ] After this, `app/Console/Kernel.php` can be deleted — no scheduled commands exist, `routes/console.php` already required

---

## Phase 6 – Service Providers

- [ ] `RouteServiceProvider.php` — remove `protected $namespace` and all `->namespace($this->namespace)` calls, then delete file (routes move to `bootstrap/app.php`)
- [ ] `AuthServiceProvider.php` — remove `$this->registerPolicies()` call, delete file (no custom gates defined)
- [ ] `EventServiceProvider.php` — delete file — only contains default `Registered` event which is auto-handled by Laravel 11+
- [ ] `config/app.php` — see Phase 10

---

## Phase 7 – Routes

- [ ] `routes/web.php` — convert ALL string-based controller references to FQCN syntax — affects every route in the file:
  ```php
  // Before
  Route::resource('notes', 'NotesController');
  // After
  Route::resource('notes', NotesController::class);
  ```
- [ ] `routes/web.php` — add missing `use` imports for all controllers at top of file
- [ ] `routes/web.php` — fix all `redirect()->action('ControllerName@method', [...])` calls in `SellerController` — removed in Laravel 9, replace with `redirect()->route('routeName', [...])`
- [ ] `routes/api.php` — already uses correct `[ControllerClass::class, 'method']` syntax ✅

---

## Phase 8 – Models

- [ ] `User.php` — move `protected $dates = ['deleted_at']` into `$casts` as `'deleted_at' => 'datetime'`
- [ ] `Brand.php` — move `$dates` into `$casts` AND add `use SoftDeletes` trait (currently missing)
- [ ] `Listing.php` — move `$dates` into `$casts` AND add `use SoftDeletes` trait (currently missing) — NOTE: `Listing.php` has no `use DB` import, do not remove anything
- [ ] `Seller.php` — move `$dates` into `$casts`; keep `use DB` (actively used)
- [ ] `BrandRequest.php` — move `$dates` into `$casts` AND add `use SoftDeletes` trait (currently missing)
- [ ] `Question.php` — move `$dates` into `$casts` AND add `use SoftDeletes` trait (currently missing)
- [ ] `Users.php` — move `$dates` into `$casts` (already has `use SoftDeletes` ✅)
- [ ] `Store.php` — no `$dates` issue ✅
- [ ] `StoreEmployee.php` — no `$dates` issue ✅
- [ ] `StoreInventory.php` — no `$dates` issue ✅

---

## Phase 9 – Controllers

- [ ] `Controller.php` — remove `DispatchesJobs` trait and its import (removed in L11)
- [ ] `SellerController.php` — fix all `redirect()->action('SellerController@createListing', [...])` calls — 3 occurrences in `storeListingAddition`, `storeListingData`, `myListingPhotoStore` — replace with `redirect()->route('createListing', [...])`
- [ ] `SellerController.php` — remove dead `$validatedData->fails()` block in `store()` — `$this->validate()` throws on failure and never returns a validator object
- [ ] `SellerController.php` — fix `unlink()` bug in `myListingPhotoStore()` — image_1, image_3, image_4, image_5 use `unlink(url('/').'/uploads/...')` which passes a URL not a filesystem path — replace with `unlink(public_path('/uploads/...'))`
- [ ] All API controllers — no breaking changes, JWT usage compatible with `php-open-source-saver/jwt-auth ^2.0` ✅

---

## Phase 10 – config/app.php

- [ ] Remove entire `providers` array EXCEPT keep `\PHPOpenSourceSaver\JWTAuth\Providers\LaravelServiceProvider::class` — jwt-auth is NOT auto-discovered and must be explicitly registered (or move to `bootstrap/app.php`)
- [ ] `Spatie\Permission\PermissionServiceProvider::class` can be removed — Spatie v6 supports auto-discovery
- [ ] Remove entire `aliases` array EXCEPT keep `JWTAuth` and `JWTFactory` — move them to `bootstrap/app.php`

---

## Phase 11 – config/auth.php

- [ ] Add `'password_timeout' => 10800` as a top-level key in the array — NOT inside the `passwords` section
- [ ] No other changes needed ✅

---

## Phase 12 – Factories

- [ ] `UserFactory.php` — change `$this->faker->name` to `$this->faker->name()` and `$this->faker->unique()->safeEmail` to `$this->faker->unique()->safeEmail()`
- [ ] `NotesFactory.php` — no faker property issues found ✅
- [ ] Other factories — no issues found ✅

---

## Phase 13 – Seeders

- [ ] `UsersAndNotesSeeder.php` — uses `Faker\Factory` directly — `fakerphp/faker` has identical API, no code change needed, just package rename in `composer.json`
- [ ] All other seeders — no issues found ✅

---

## Phase 14 – Tests

- [ ] `phpunit.xml` — remove `backupStaticAttributes`, `convertErrorsToExceptions`, `convertNoticesToExceptions`, `convertWarningsToExceptions` attributes from root `<phpunit>` element
- [ ] `phpunit.xml` — update schema URL to `https://schema.phpunit.de/11.0/phpunit.xsd`
- [ ] `phpunit.xml` — rename `MAIL_DRIVER` to `MAIL_MAILER`
- [ ] `phpunit.xml` — change `<extension class="Tests\Bootstrap"/>` to `<bootstrap class="Tests\Bootstrap"/>`
- [ ] `Bootstrap.php` — rewrite: replace `BeforeFirstTestHook` and `AfterLastTestHook` interfaces (removed in PHPUnit 10) with `PHPUnit\Runner\Extension\Extension` interface and implement `bootstrap(PHPUnit\Runner\Extension\Facade $facade, PHPUnit\TextUI\Configuration\Configuration $configuration, PHPUnit\Runner\Extension\ParameterCollection $parameters): void`
- [ ] `UserTest.php` line 18 — rename `registerPermissions()` to `forgetCachedPermissions()` — method was renamed in `spatie/laravel-permission` v6
- [ ] All other test files — no breaking changes found ✅

---

## Phase 15 – JWT Migration

- [ ] Remove `tymon/jwt-auth` from `composer.json`, add `php-open-source-saver/jwt-auth ^2.0`
- [ ] Run `php artisan jwt:secret` after install
- [ ] `User.php` — change `use Tymon\JWTAuth\Contracts\JWTSubject` to `use PHPOpenSourceSaver\JWTAuth\Contracts\JWTSubject`
- [ ] All API controllers — change `Tymon\JWTAuth` namespace imports to `PHPOpenSourceSaver\JWTAuth`

---

## Phase 16 – Final Verification

- [ ] `php artisan config:clear && php artisan cache:clear && php artisan route:clear && php artisan view:clear`
- [ ] `php artisan migrate:fresh --seed`
- [ ] `php artisan route:list` — verify all routes
- [ ] `php artisan about` — verify Laravel 12 and PHP 8.4
- [ ] `php vendor/bin/phpunit --stop-on-failure`
- [ ] Manual test: web login, JWT API auth, media upload, roles/permissions, store/employee flow
