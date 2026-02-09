<?php

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
*/

$app = new Illuminate\Foundation\Application(
    $_ENV['APP_BASE_PATH'] ?? dirname(__DIR__)
);

/*
|--------------------------------------------------------------------------
| Clear services cache if it references missing Collision (dev-only package)
|--------------------------------------------------------------------------
| When production runs "composer install --no-dev", Collision is not installed
| but a cached services.php may still reference it. Remove the cache so
| Laravel can rediscover providers without Collision.
*/
$servicesPath = $app->basePath('bootstrap/cache/services.php');
if (file_exists($servicesPath)) {
    $content = @file_get_contents($servicesPath);
    if ($content !== false
        && str_contains($content, 'CollisionServiceProvider')
        && ! class_exists('NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider', false)) {
        @unlink($servicesPath);
    }
}

/*
|--------------------------------------------------------------------------
| Bind Important Interfaces
|--------------------------------------------------------------------------
*/

$app->singleton(
    Illuminate\Contracts\Http\Kernel::class,
    App\Http\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

/*
|--------------------------------------------------------------------------
| Return The Application
|--------------------------------------------------------------------------
|
| This script returns the application instance. The instance is given to
| the calling script so we can separate the building of the instances
| from the actual running of the application and sending responses.
|
*/

return $app;
