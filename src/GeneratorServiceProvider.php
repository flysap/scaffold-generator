<?php

namespace Flysap\ScaffoldGenerator;

use Cartalyst\Tags\TagsServiceProvider;
use Cviebrock\EloquentSluggable\SluggableServiceProvider;
use Eloquent\ImageAble\ImageAbleServiceProvider;
use Eloquent\Meta\MetaServiceProvider;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;
use Laravel\Meta\MetaSeoServiceProvider;

class GeneratorServiceProvider extends ServiceProvider {

    /**
     * On boot's application load package requirements .
     */
    public function boot() {
        $this->loadRoutes()
            ->loadViews();

        /** Adding namespace to temp modules path to render menu . */
        app('menu-manager')->addNamespace(
            'storage/' . config('scaffold-generator.temp_path')
        );
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        $this->loadConfiguration();

        app()->singleton(
            'stub-generator',
            StubGenerator::class
        );

        app()->singleton('scaffold-package', function() {
            return (new PackageManager)
                ->setPackages(config('scaffold-generator.packages'))
                ->setDefaultPackages(config('scaffold-generator.default_packages'));
        });

        #@todo we need that ?

        /** Scaffold manager . */
        $this->app->singleton('scaffold-manager', function ($app) {
            return new ScaffoldManager;
        });

        /** Register service providers depency . */
        $this->registerPackageServices();
    }

    /**
     * Load routes .
     *
     * @return $this
     */
    protected function loadRoutes() {
        if (! $this->app->routesAreCached()) {
            require __DIR__ . '/../routes.php';
        }

        return $this;
    }

    /**
     * Load configuration .
     *
     * @return $this
     */
    protected function loadConfiguration() {
        Support\set_config_from_yaml(
            __DIR__ . '/../configuration/general.yaml' , 'scaffold-generator'
        );

        return $this;
    }

    /**
     * Load views .
     */
    protected function loadViews() {
        $this->loadViewsFrom(__DIR__ . '/../views', 'scaffold-generator');

        return $this;
    }

    /**
     * Register service provider dependencies .
     *
     */
    protected function registerPackageServices() {
        $providers = [
            SluggableServiceProvider::class,
            MetaSeoServiceProvider::class,
            MetaServiceProvider::class,
            ImageAbleServiceProvider::class,
            TagsServiceProvider::class,
        ];

        array_walk($providers, function($provider) {
            app()->register($provider);
        });
    }
}