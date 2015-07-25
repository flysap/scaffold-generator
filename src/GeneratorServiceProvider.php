<?php

namespace Flysap\ScaffoldGenerator;

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
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {

        /** Stub generator . */
        $this->app->singleton('stub-generator', function() {
            return new StubGenerator(
                new Filesystem()
            );
        });

        /** Scaffold manager . */
        $this->app->singleton('scaffold-generator', function($app) {
            return new ScaffoldManager(
                $app['stub-generator']
            );
        });

    }

    /**
     * Load routes .
     *
     * @return $this
     */
    protected function loadRoutes() {
        if (! $this->app->routesAreCached()) {
            require __DIR__.'/../routes.php';
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
        $this->loadViewsFrom(__DIR__.'/../views', 'scaffold-generator');

        return $this;
    }
}