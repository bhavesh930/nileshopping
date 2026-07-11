<?php declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Controls the HTTP route that your GraphQL server responds to.
    | You may set `route` => false, to disable the default route
    | registration and take full control.
    |
    */

    'route' => [
        /*
         * The URI the endpoint responds to, e.g. mydomain.com/graphql.
         */
        'uri' => '/graphql',

        /*
         * Lighthouse creates a named route for convenient URL generation and redirects.
         */
        'name' => 'graphql',

        /*
         * Beware that middleware defined here runs before the GraphQL execution phase,
         * make sure to return spec-compliant responses in case an error is thrown.
         */
        'middleware' => [
            // Always set the `Accept: application/json` header.
            Nuwave\Lighthouse\Http\Middleware\AcceptJson::class,

            // Logs in a user if they are authenticated. In contrast to Laravel's 'auth'
            // middleware, this delegates auth and permission checks to the field level.
            Nuwave\Lighthouse\Http\Middleware\AttemptAuthentication::class,
        ],

        /*
         * Sets the route under the `api` prefix so it resolves at /api/graphql.
         * Remove the 'api' middleware if you want to handle throttle yourself.
         */
        'prefix' => 'api',

        // 'domain' => '',
        // 'where' => [],
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | The guards to use for authenticating GraphQL requests, if needed.
    | Used in directives such as `@guard` or the `AttemptAuthentication` middleware.
    | Falls back to the Laravel default if `null`.
    |
    */

    'guards' => ['api'],

    /*
    |--------------------------------------------------------------------------
    | Schema Path
    |--------------------------------------------------------------------------
    |
    | Path to your .graphql schema file.
    | Additional schema files may be imported from within that file.
    |
    */

    'schema_path' => base_path('graphql/schema.graphql'),

    /*
    |--------------------------------------------------------------------------
    | Schema Cache
    |--------------------------------------------------------------------------
    |
    | A large part of schema generation consists of parsing and AST manipulation.
    | This operation is very expensive, so it is highly recommended enabling
    | caching of the final schema to optimize performance of large schemas.
    |
    */

    'schema_cache' => [
        /*
         * Setting to true enables schema caching.
         */
        'enable' => env('LIGHTHOUSE_SCHEMA_CACHE_ENABLE', false),

        /*
         * File path to store the lighthouse schema.
         */
        'path' => env('LIGHTHOUSE_SCHEMA_CACHE_PATH', base_path('bootstrap/cache/lighthouse-schema.php')),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Directive Tags
    |--------------------------------------------------------------------------
    */
    'cache_directive_tags' => false,

    /*
    |--------------------------------------------------------------------------
    | Query Cache
    |--------------------------------------------------------------------------
    */

    'query_cache' => [
        'enable' => env('LIGHTHOUSE_QUERY_CACHE_ENABLE', true),
        'store'  => env('LIGHTHOUSE_QUERY_CACHE_STORE', null),
        'ttl'    => env('LIGHTHOUSE_QUERY_CACHE_TTL', 24 * 60 * 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Cache
    |--------------------------------------------------------------------------
    */

    'validation_cache' => [
        'enable' => env('LIGHTHOUSE_VALIDATION_CACHE_ENABLE', false),
        'store'  => env('LIGHTHOUSE_VALIDATION_CACHE_STORE', null),
        'ttl'    => env('LIGHTHOUSE_VALIDATION_CACHE_TTL', 24 * 60 * 60),
    ],

    /*
    |--------------------------------------------------------------------------
    | Parse source location
    |--------------------------------------------------------------------------
    */

    'parse_source_location' => true,

    /*
    |--------------------------------------------------------------------------
    | Namespaces
    |--------------------------------------------------------------------------
    */

    'namespaces' => [
        'models'        => ['App', 'App\\Models'],
        'queries'       => 'App\\GraphQL\\Queries',
        'mutations'     => 'App\\GraphQL\\Mutations',
        'subscriptions' => 'App\\GraphQL\\Subscriptions',
        'types'         => 'App\\GraphQL\\Types',
        'interfaces'    => 'App\\GraphQL\\Interfaces',
        'unions'        => 'App\\GraphQL\\Unions',
        'scalars'       => 'App\\GraphQL\\Scalars',
        'directives'    => 'App\\GraphQL\\Directives',
        'validators'    => 'App\\GraphQL\\Validators',
    ],

    /*
    |--------------------------------------------------------------------------
    | Security
    |--------------------------------------------------------------------------
    */

    'security' => [
        'max_query_complexity'  => GraphQL\Validator\Rules\QueryComplexity::DISABLED,
        'max_query_depth'       => GraphQL\Validator\Rules\QueryDepth::DISABLED,
        'disable_introspection' => (bool) env('LIGHTHOUSE_SECURITY_DISABLE_INTROSPECTION', false)
            ? GraphQL\Validator\Rules\DisableIntrospection::ENABLED
            : GraphQL\Validator\Rules\DisableIntrospection::DISABLED,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */

    'pagination' => [
        'default_count' => null,
        'max_count'     => null,
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug
    |--------------------------------------------------------------------------
    |
    | 7 = RETHROW_INTERNAL_EXCEPTIONS | INCLUDE_TRACE | INCLUDE_DEBUG_MESSAGE
    | Use 0 in production.
    |
    */

    'debug' => env('LIGHTHOUSE_DEBUG', GraphQL\Error\DebugFlag::INCLUDE_DEBUG_MESSAGE | GraphQL\Error\DebugFlag::INCLUDE_TRACE),

    /*
    |--------------------------------------------------------------------------
    | Error Handlers
    |--------------------------------------------------------------------------
    */

    'error_handlers' => [
        Nuwave\Lighthouse\Execution\AuthenticationErrorHandler::class,
        Nuwave\Lighthouse\Execution\AuthorizationErrorHandler::class,
        Nuwave\Lighthouse\Execution\ValidationErrorHandler::class,
        Nuwave\Lighthouse\Execution\ReportingErrorHandler::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Field Middleware
    |--------------------------------------------------------------------------
    */

    'field_middleware' => [
        Nuwave\Lighthouse\Schema\Directives\TrimDirective::class,
        Nuwave\Lighthouse\Schema\Directives\ConvertEmptyStringsToNullDirective::class,
        Nuwave\Lighthouse\Schema\Directives\SanitizeDirective::class,
        Nuwave\Lighthouse\Validation\ValidateDirective::class,
        Nuwave\Lighthouse\Schema\Directives\TransformArgsDirective::class,
        Nuwave\Lighthouse\Schema\Directives\SpreadDirective::class,
        Nuwave\Lighthouse\Schema\Directives\RenameArgsDirective::class,
        Nuwave\Lighthouse\Schema\Directives\DropArgsDirective::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Global ID
    |--------------------------------------------------------------------------
    */

    'global_id_field' => 'id',

    /*
    |--------------------------------------------------------------------------
    | Persisted Queries
    |--------------------------------------------------------------------------
    */

    'persisted_queries' => true,

    /*
    |--------------------------------------------------------------------------
    | Transactional Mutations
    |--------------------------------------------------------------------------
    */

    'transactional_mutations' => true,

    /*
    |--------------------------------------------------------------------------
    | Mass Assignment Protection
    |--------------------------------------------------------------------------
    */

    'force_fill' => true,

    /*
    |--------------------------------------------------------------------------
    | Batchload Relations
    |--------------------------------------------------------------------------
    */

    'batchload_relations' => true,

    /*
    |--------------------------------------------------------------------------
    | Shortcut Foreign Key Selection
    |--------------------------------------------------------------------------
    */

    'shortcut_foreign_key_selection' => false,

    /*
    |--------------------------------------------------------------------------
    | GraphQL Subscriptions
    |--------------------------------------------------------------------------
    */

    'subscriptions' => [
        'queue_broadcasts'    => env('LIGHTHOUSE_QUEUE_BROADCASTS', true),
        'broadcasts_queue_name' => env('LIGHTHOUSE_BROADCASTS_QUEUE_NAME', null),
        'storage'             => env('LIGHTHOUSE_SUBSCRIPTION_STORAGE', 'redis'),
        'storage_ttl'         => env('LIGHTHOUSE_SUBSCRIPTION_STORAGE_TTL', null),
        'encrypted_channels'  => env('LIGHTHOUSE_SUBSCRIPTION_ENCRYPTED', false),
        'broadcaster'         => env('LIGHTHOUSE_BROADCASTER', 'pusher'),
        'broadcasters' => [
            'log'    => ['driver' => 'log'],
            'echo'   => [
                'driver'     => 'echo',
                'connection' => env('LIGHTHOUSE_SUBSCRIPTION_REDIS_CONNECTION', 'default'),
                'routes'     => Nuwave\Lighthouse\Subscriptions\SubscriptionRouter::class . '@echoRoutes',
            ],
            'pusher' => [
                'driver'     => 'pusher',
                'connection' => 'pusher',
                'routes'     => Nuwave\Lighthouse\Subscriptions\SubscriptionRouter::class . '@pusher',
            ],
            'reverb' => [
                'driver'     => 'pusher',
                'connection' => 'reverb',
                'routes'     => Nuwave\Lighthouse\Subscriptions\SubscriptionRouter::class . '@reverb',
            ],
        ],
        'exclude_empty' => env('LIGHTHOUSE_SUBSCRIPTION_EXCLUDE_EMPTY', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Defer
    |--------------------------------------------------------------------------
    */

    'defer' => [
        'max_nested_fields' => 0,
        'max_execution_ms'  => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Apollo Federation
    |--------------------------------------------------------------------------
    */

    'federation' => [
        'entities_resolver_namespace' => 'App\\GraphQL\\Entities',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tracing
    |--------------------------------------------------------------------------
    */

    'tracing' => [
        'driver' => Nuwave\Lighthouse\Tracing\ApolloTracing\ApolloTracing::class,
    ],
];
