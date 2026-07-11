# NileShopping

Laravel admin / e-commerce backend built on top of the CoreUI Free Laravel admin template. Ships a web admin (BREAD-style CRUD, menu builder, roles, media library) plus a JWT-authenticated mobile API.

## Stack

- **PHP** `^8.3` (locked to `8.3.23` in `composer.json` `config.platform.php`).
- **Laravel** `^12.0`.
- **Auth**: web sessions + `tymon/jwt-auth ^2.0` for the mobile API. Existing `JWT_SECRET` in `.env` is preserved — do **not** run `php artisan jwt:secret` (it invalidates every issued token).
- **Permissions**: `spatie/laravel-permission ^6.0` (namespace is `Spatie\Permission\Middleware\…`, not the old `Middlewares\…`).
- **Media**: `spatie/laravel-medialibrary ^11.0`.
- **Frontend**: Laravel UI (`laravel/ui ^4.0`), Bootstrap/CoreUI assets compiled via `webpack.mix.js`.
- **DB**: MySQL via Laragon (`d:\laragon\www\nileshopping`).
- **Tests**: PHPUnit `^11`.

## Local environment quirks

- This machine has **two PHP installs**: system PHP 8.1.6 (default on PATH) and PHP 8.3.23 at `D:\laragon\bin\php\php-8.3.23-Win32-vs16-x64`. Composer and `php artisan`/`vendor/bin/phpunit` **must** be run from a shell where 8.3 is first on `PATH`, otherwise everything breaks with parse errors on PHP 8.3-only syntax. The user runs commands themselves in a 8.3 terminal — do not assume `php` in the agent shell is 8.3.
- Line endings on edited files have shown up as `\r\r\n` in the past. Prefer `Edit`/`Write` (which normalise) over `sed`-style patches; if an edit looks corrupted, rewrite the file.
- Laragon serves the app at `http://localhost/nileShopping/public`.

## Repo layout (only the non-obvious bits)

- `bootstrap/app.php` — Laravel 11/12 fluent bootstrap. Middleware aliases live here, **not** in `app/Http/Kernel.php` (kernel was kept around but middleware aliases were migrated). Spatie role middleware is registered as `'role' => \Spatie\Permission\Middleware\RoleMiddleware::class`.
- `app/Helpers/StoreHelper.php` — autoloaded via `composer.json` `autoload.files`.
- `app/MenuBuilder/` — custom menu system (has its own tests under `tests/Unit/Menu*Test.php`).
- `app/Http/Controllers/Api/` — JWT-protected mobile endpoints (`UserAuthController`, `JWTAuthController`, `CategoryAuthController`, `ListingAuthController`). All use `Tymon\JWTAuth\…` namespaces — do **not** swap to `php-open-source-saver/jwt-auth`, the upgrade plan deliberately stayed on `tymon`.
- `routes/web.php` — uses FQCN array syntax `[Controller::class, 'method']` (converted from L8 string syntax). Several large commented-out route groups are intentional and must be preserved.
- `tests/Bootstrap.php` — implements PHPUnit 11 `Extension` interface (rewritten from removed `BeforeFirstTestHook`/`AfterLastTestHook`). Wired in `phpunit.xml` as `<extensions><bootstrap class="Tests\Bootstrap"/></extensions>`.
- `phpunit.xml` only declares the `Unit` testsuite — there is **no** `tests/Feature` directory; do not re-add it.

## Working in this repo

- **Preserve existing comments** (docblocks, section headers, commented-out code) when editing legacy files — the user has corrected the previous AI for stripping them. Change syntax only, not surrounding text.
- **Don't dispatch large refactors.** This is a Laravel 8 → 12 jump that the user is reviewing phase-by-phase. Make the smallest change that completes the current step and stop for confirmation before moving on.
- **`$dates` is gone** — all models that used `$dates = ['deleted_at']` already moved it into `$casts`. New models should follow the `$casts` pattern.
- **Service providers** (`AuthServiceProvider`, `EventServiceProvider`, `RouteServiceProvider`) are stripped down for L11+ — `registerPolicies()`, `parent::boot()`, and the `$namespace` property were removed but the files are still present.
- **`config/app.php`** still contains the `providers`/`aliases` arrays from L8; they were intentionally left because nothing in the upgrade required removing them. Don't strip them on a whim.

## Common commands (run in a PHP 8.3 shell)

```cmd
php artisan about                    :: confirm Laravel 12 + PHP 8.3.23
php artisan route:list               :: 210 routes expected
php artisan migrate                  :: never run migrate:fresh on the dev DB without asking — it has data
php vendor/bin/phpunit               :: PHPUnit 11.5.x; only the Unit suite exists
php vendor/bin/phpunit --filter UserTest
```

## Branches

- `main` — last known L8 state on origin.
- `backup/laravel8-original` — frozen pre-upgrade snapshot (do not touch).
- `upgrade/laravel-12` — active upgrade branch. All work happens here.

## Reference docs in repo

- `UPGRADE_LARAVEL_12.md` — the phase-by-phase upgrade plan that's been driving the work; check before changing upgrade-related files.
- `q-dev-chat-2026-05-09.md` — transcript of the original upgrade session (for historical context only).

## Schema debt (background)

`migrate:fresh` originally halted because 13 production tables (`listings`, `brands`, `sellers`, `orders`, `carts`, etc.) were never written as migrations — they only existed in the live `nile_db` from a SQL dump. Resolved by `database/migrations/2014_10_11_000000_baseline_schema_from_dump.php` which creates them idempotently. Plus three smaller patches:

- `2022_02_12_172018_adddefaulttimecolumnuseraddress-table.php` — wrapped in `if (! Schema::hasColumn(…))` (the underlying `create_user_address_table` already calls `$table->timestamps()`).
- `2022_03_04_064930_add_image_menu_element_table.php` — moved from `database/` into `database/migrations/`, made idempotent.
- `2026_05_09_100000_upgrade_media_table_for_spatie_v11.php` — adds `generated_conversions`, replaces the integer `uuid` column with a real UUID, makes `conversions_disk` nullable. Spatie MediaLibrary v8 → v11 schema gap.

`migrate:fresh` now succeeds against an empty DB. New schema work going forward must add real migrations — don't fall back to "edit the dump."

## Architectural concerns / tech debt

These are observations from auditing the codebase during the L8 → L12 upgrade. None block the upgrade, but a future maintainer should know about them:

### Auth / permissions (highest priority)

- **Two parallel auth systems coexist.** `users.menuroles` is a comma-separated string (`'user,admin'`) checked directly in `App\Http\Middleware\GetMenu` and elsewhere; Spatie `model_has_roles` is the relational version checked by `RoleMiddleware`. Pick one and remove the other — the hybrid is a maintenance trap.
- **Permissions are coupled to auto-generated form IDs** (`'browse bread 1'`, `'edit bread 2'`, …). If a Form record is recreated with a new ID, every permission grant for it silently breaks. Permissions should reference something stable like `form.name`.
- **Spatie v6 throws where v3 returned false.** `hasPermissionTo()` now throws `PermissionDoesNotExist` when the permission isn't registered. `ResourceController` already wraps every call in try/catch; `FormService::getBreadRoles` was patched to match. Any new `hasPermissionTo` call elsewhere needs the same guard.

### Duplicate / unfinished concepts

- **`User` and `Users` models both exist** in `app/Models/`. Resolve which is canonical and delete the other.
- **`store` and `stores` tables both exist** with different schemas and different models (`Store`, `StoreEmployee`, `StoreInventory`). Looks like an unfinished refactor.
- **`whishlist` is a typo** in the table name. Renaming costs a migration + a model rename — pay it down on the next sprint touching that area.

### Database hygiene

- **Mixed charsets across tables.** `users`, `whishlist`, `reviews`, `listing_sizechart` are `latin1`; most others are `utf8`; newer tables are `utf8mb4_unicode_ci`. Anything 4-byte (emoji, some Asian scripts) won't store in the `latin1` tables. A coordinated `ALTER TABLE … CONVERT TO CHARACTER SET utf8mb4` migration would fix this.
- **Schema is not the source of truth for production setup.** Make sure new dev onboarding is `git clone → composer install → php artisan migrate`, not "ask someone for `nile_db (1).sql`."

### Code hygiene

- **Mixed line endings** (`CRLF` and `\r\r\n`) in many source files. Adding `.editorconfig` and a one-shot normalization commit (`git ls-files | xargs dos2unix`-style) prevents a class of edit-time bugs.
- **"Unit" tests are actually feature tests** — they hit HTTP routes, the DB, and views. Path is `tests/Unit/` but they all extend `TestCase` (not `PHPUnit\Framework\TestCase`) and use `DatabaseMigrations`/`RefreshDatabase`/`DatabaseTruncation`. The naming masks what they actually exercise.
- **Inline debug comments** in `routes/web.php` (`//echo Auth::user();die()`, etc.) and large commented-out route blocks (Seller Dashboard) suggest no PR review enforcement.

### Test suite

- Trait choice per file is documented at the bottom of `tests/Unit/` reasoning. **`DatabaseTruncation` doesn't reliably reset MySQL `AUTO_INCREMENT` for the `media` table** in this environment (cause not pinned down). The fix applied: tests no longer assert hardcoded `id=1` values; they capture the actual returned ID. Apply the same pattern for any new test that creates records.
- The whole suite reports **189 risky** with the message *"Test code or tested code removed error handlers other than its own"* — a PHPUnit 11 strict-mode warning, not a real failure. Some code in the request pipeline (likely Symfony's error handler or Whoops) replaces and doesn't restore the same handler. Address by either (a) adding `failOnRisky="false"` to `phpunit.xml`, or (b) tracking down the offending handler swap. Tests pass either way.

## Project tree

Trimmed (vendor, node_modules, .git, storage caches, public/css|js|img|fonts|vendors, public/uploads/* contents, resources/assets, public/coreui-* omitted):

```
nileshopping/
├── CLAUDE.md
├── UPGRADE_LARAVEL_12.md          ← phase-by-phase upgrade plan
├── q-dev-chat-2026-05-09.md       ← upgrade session transcript
├── README.md                      ← stock CoreUI Laravel template README
├── artisan
├── composer.json / composer.lock
├── package.json / package-lock.json
├── webpack.mix.js
├── phpunit.xml
├── server.php / index.php / default.php
├── styleci.yml / editorconfig.txt / gitignore.txt / gitattributes.txt
├── app/
│   ├── Console/
│   │   └── Kernel.php             ← legacy L8 kernel, can be removed under L11+ pattern
│   ├── Exceptions/                ← Handler.php (legacy)
│   ├── Helpers/
│   │   └── StoreHelper.php        ← autoloaded via composer.json files[]
│   ├── Http/
│   │   ├── Kernel.php             ← legacy L8 kernel; aliases moved to bootstrap/app.php
│   │   ├── Controllers/
│   │   │   ├── Controller.php
│   │   │   ├── BreadController.php / BreadTest target
│   │   │   ├── BrandController.php
│   │   │   ├── CategoryController.php
│   │   │   ├── ListingController.php
│   │   │   ├── MailController.php
│   │   │   ├── MediaController.php
│   │   │   ├── MenuController.php / MenuElementController.php
│   │   │   ├── NotesController.php
│   │   │   ├── OrderController.php
│   │   │   ├── QuestionController.php
│   │   │   ├── ResourceController.php
│   │   │   ├── RolesController.php
│   │   │   ├── SellerController.php / SellerRegistrationController.php
│   │   │   ├── StoreController.php / StoreEmployeeController.php
│   │   │   ├── UsersController.php
│   │   │   ├── Auth/              ← Login / Register / Reset / Verification
│   │   │   ├── admin/             ← admin\UsersController
│   │   │   └── Api/               ← JWT-protected mobile endpoints
│   │   │       ├── JWTAuthController.php
│   │   │       ├── UserAuthController.php
│   │   │       ├── CategoryAuthController.php
│   │   │       └── ListingAuthController.php
│   │   ├── Menus/                 ← GetSidebarMenu, MenuInterface
│   │   └── Middleware/            ← Authenticate, RedirectIfAuthenticated, TrustProxies,
│   │                                PreventRequestsDuringMaintenance, Admin, GetMenu,
│   │                                EmployeeMiddleware, EmployeePermission, etc.
│   ├── MenuBuilder/               ← FreelyPositionedMenus, MenuBuilder, RenderFromDatabaseData
│   ├── Models/                    ← Brand, BrandRequest, Category, EmailTemplate, Example,
│   │                                Folder, Form, FormField, Listing, Menulist, Menurole,
│   │                                Menus, Notes, Question, QuestionOption, RoleHierarchy,
│   │                                Seller, Status, Store, StoreEmployee, StoreInventory,
│   │                                User, Users
│   ├── Providers/                 ← Auth/Event/Route service providers (slimmed for L11+)
│   └── Services/                  ← EditMenuViewService, FormService, RemoveFolderService,
│                                    ResourceService, RolesService
├── bootstrap/
│   ├── app.php                    ← L11/12 fluent bootstrap (middleware aliases live here)
│   └── cache/
├── build/                         ← compiled webpack output
├── config/                        ← auth, app, database, media-library, hashing, etc.
├── database/
│   ├── 2022_03_04_064930_add_image_menu_element_table.php   ← ⚠ wrong dir, never runs
│   ├── factories/                 ← UserFactory, NotesFactory, etc.
│   ├── migrations/                ← ~30 migrations; see "Schema debt" above
│   └── seeders/
├── public/                        ← entry + uploaded media (uploads/, brands/, listings/, …)
├── resources/
│   ├── assets/                    ← raw CoreUI assets (compiled into public/)
│   ├── js/                        ← components, coreui sources
│   ├── lang/en/
│   ├── sass/                      ← vendors/pace-progress, etc.
│   ├── vendors/pace-progress/
│   └── views/                     ← Blade templates: auth, brand, dashboard, …
├── routes/
│   ├── api.php                    ← already FQCN style
│   ├── channels.php
│   ├── console.php
│   └── web.php                    ← rewritten to FQCN array syntax during upgrade
├── storage/                       ← framework caches, app/, logs/
├── tests/
│   ├── Bootstrap.php              ← PHPUnit 11 Extension API
│   ├── CreatesApplication.php
│   ├── TestCase.php
│   └── Unit/                      ← _aCoreUITest, BreadTest, MediaTest, MenuBuilderTest,
│                                    MenuElementTest, MenuTest, NotesTest, RemoveFolderServiceTest,
│                                    RenderFromDatabaseDataTest, ResourceServiceTest,
│                                    ResourceTest, RolesServiceTest, UserTest
└── vendor/                        ← composer deps (omitted)
```