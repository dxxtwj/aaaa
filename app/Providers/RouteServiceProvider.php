<?php

namespace App\Providers;

use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapSessionAndOptionsRoutes();
        $this->mapApi0Routes();
        $this->mapOpen1Routes();
        $this->mapAdmin1Routes();
        $this->mapApi2Routes();
        $this->mapApi3Routes();
        $this->mapApiAdmin3Routes();
        $this->mapApiWeb3Routes();
        $this->mapApi4Routes();
        $this->mapApi10Routes();
        $this->mapWebRoutes();
        $this->mapManage1Routes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
             ->namespace($this->namespace)
             ->group(base_path('routes/web.php'));
    }



    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApi4Routes()
    {
        Route::prefix('v1.0/')
             ->middleware('api1.0')
             ->namespace($this->namespace)
             ->group(base_path('routes/api1.0.php'));
    }
    protected function mapApi2Routes()
    {
        Route::prefix('v2.0/')
            ->middleware('api2.0')
            ->namespace($this->namespace)
            ->group(base_path('routes/api2.0.php'));
    }
    protected function mapApi0Routes()
    {
        Route::prefix('v0.0/')
            ->middleware('api0.0')
            ->namespace($this->namespace)
            ->group(base_path('routes/api0.0.php'));
    }
    protected function mapApi10Routes()
    {
        Route::prefix('v10.0/')
            ->middleware('api10.0')
            ->namespace($this->namespace)
            ->group(base_path('routes/api10.0.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApi3Routes()
    {
        Route::prefix('v3.0/api')
            ->middleware('api3.0')
            ->namespace($this->namespace.'\V3\Api')
            ->group(base_path('routes/api3.0.php'));
    }
    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiWeb3Routes()
    {
        Route::prefix('v3.0/api/web')
            ->middleware('api3.0')
            ->namespace($this->namespace.'\V3\Api\Web')
            ->group(base_path('routes/api3.0.web.php'));
    }
    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiAdmin3Routes()
    {
        Route::prefix('v3.0/api/admin')
            ->middleware('api3.0')
            ->namespace($this->namespace.'\V3\Api\Admin')
            ->group(base_path('routes/api3.0.admin.php'));
    }
    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapOpen1Routes()
    {
        Route::prefix('v1.0/open')
            ->namespace($this->namespace.'\V1\Open')
            ->group(base_path('routes/api1.0.open.php'));
    }
    protected function mapManage1Routes()
    {
        Route::prefix('v1.0/manage')
            ->namespace($this->namespace.'\V1\Manage')
            ->group(base_path('routes/api1.0.manage.php'));
    }
    protected function mapAdmin1Routes()
    {
        Route::prefix('v1.0/admin')
            ->namespace($this->namespace.'\V1\Admin')
            ->group(base_path('routes/api1.0.admin.php'));
    }




    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapSessionAndOptionsRoutes()
    {
        Route::namespace($this->namespace)
             ->group(base_path('routes/sessionAndOptions.php'));
    }
}
