<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
 */

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();
/*************************/
/*                       */
/*    (☉д⊙)             */
/*                       */
/*  載入的 Facades 開始   */
/*                       */
/*                       */
/*                       */
/*************************/
if (!class_exists('IM')) {
    class_alias('App\Facades\IMFacade', 'IM');
}

if (!class_exists('JWTAuth')) {
    class_alias('App\JWTAuth', 'JWTAuth');
}
if (!class_exists('JWT')) {
    class_alias('Firebase\JWT\JWT', 'JWT');
}
if (!class_exists('Sms')) {
    class_alias('App\Facades\SmsFacade', 'Sms');
}
if (!class_exists('CLStorage')) {
    class_alias('App\Facades\CLStorageFacade', 'CLStorage');
}
if (!class_exists('Live')) {
    class_alias('App\Facades\LiveFacade', 'Live');
}

if (!class_exists('Route')) {
    class_alias('Illuminate\Support\Facades\Route', 'Route');
}

if (!class_exists('Cookie')) {
    class_alias('Illuminate\Support\Facades\Cookie', 'Cookie');
}
if (!class_exists('File')) {
    class_alias('Illuminate\Support\Facades\File', 'File');
}

/*************************/
/*                       */
/*    (☉д⊙)             */
/*                       */
/*  載入的 Facades 結束   */
/*                       */
/*                       */
/*                       */
/*************************/

$app->withEloquent();
/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
 */

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);
$app->singleton(
    'PaymentManager',
    App\Services\Payments\PaymentManager::class
);
$app->singleton(
    'NotifyManager',
    App\Services\Payments\NotifyManager::class
);

/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
 */

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

// $app->routeMiddleware([
//     'auth' => App\Http\Middleware\Authenticate::class,
// ]);
$app->routeMiddleware([
    'auth' => Illuminate\Auth\Middleware\Authenticate::class,
]);
$app->routeMiddleware([
    'throttle' => Illuminate\Routing\Middleware\ThrottleRequests::class,
]);
$app->routeMiddleware([
    'jwt' => App\Http\Middleware\JWT::class,
]);
$app->routeMiddleware([
    'maintain' => App\Http\Middleware\Maintain::class,
]);
/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
 */

$app->register(App\Providers\AppServiceProvider::class);
$app->register(Illuminate\Cookie\CookieServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\CustomResponseServiceProvider::class);
$app->register(App\Providers\FormRequestServiceProvider::class);
$app->register(Illuminate\Redis\RedisServiceProvider::class);
$app->register(Illuminate\Queue\QueueServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\LiveServiceProvider::class);

$app->configure('repository');
$app->singleton(Illuminate\Session\SessionManager::class, function () use ($app) {
    return $app->loadComponent('session', Illuminate\Session\SessionServiceProvider::class, 'session');
});

$app->singleton('session.store', function () use ($app) {
    return $app->loadComponent('session', Illuminate\Session\SessionServiceProvider::class, 'session.store');
});
// $app->register(Illuminate\Mail\MailServiceProvider::class);

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
 */

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

$app->router->group([
    'namespace' => 'App\Http\Controllers\API',
    'prefix' => 'api',
    // 'middleware' => ['throttle:60,1'],
], function ($router) {
    require __DIR__ . '/../routes/api.php';
});

return $app;
