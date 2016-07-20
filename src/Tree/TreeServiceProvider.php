<?php

namespace ScoLib\Tree;


use Illuminate\Support\ServiceProvider;

class TreeServiceProvider extends ServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the application events.
     *
     * @return void
     */
    public function boot()
    {
        // Publish config files
        $this->publishes([
            __DIR__ . '/../config/config.php' => config_path('tree.php'),
        ]);

        // Register blade directives
        $this->bladeDirectives();
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerTree();

    }

    /**
     * Register the application bindings.
     *
     * @return void
     */
    private function registerTree()
    {
        $this->app->bind('tree', function ($app) {
            return new Tree($app);
        });

        $this->app->alias('tree', Tree::class);
    }




}
