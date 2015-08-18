<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Parsers\Field;
use Flysap\ScaffoldGenerator\Parsers\Relation;
use Illuminate\Support\ServiceProvider;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Yaml\Yaml;

class GeneratorServiceProvider extends ServiceProvider {

    /**
     * On boot's application load package requirements .
     */
    public function boot() {
        $this->loadRoutes()
            ->loadConfiguration()
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

        /** Stub generator . */
        $this->app->singleton('stub-generator', function () {
            return new StubGenerator(
                new Filesystem()
            );
        });

        /** Scaffold manager . */
        $this->app->singleton('scaffold-manager', function ($app) {
            return new ScaffoldManager;
        });

        /** Register field parser. */
        $this->app->singleton('field-parser', function() {
            return new Field(
                config('scaffold-generator.fields_type_alias')
            );
        });

        /** Register field parser. */
        $this->app->singleton('relation-parser', function() {
            return new Relation;
        });

        /**
         * Register generator factory .
         */
        $this->app->singleton('generator', function() {
            return new Generator;
        });
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
        $array = Yaml::parse(file_get_contents(
            __DIR__ . '/../configuration/general.yaml'
        ));

        $config = $this->app['config']->get('scaffold-generator', []);

        $this->app['config']->set('scaffold-generator', array_merge((array)$array, $config));

        return $this;
    }

    /**
     * Load views .
     */
    protected function loadViews() {
        $this->loadViewsFrom(__DIR__ . '/../views', 'scaffold-generator');

        return $this;
    }
}