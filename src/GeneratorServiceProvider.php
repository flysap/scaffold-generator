<?php

namespace Flysap\ScaffoldGenerator;

use Cartalyst\Tags\TagsServiceProvider;
use Cviebrock\EloquentSluggable\SluggableServiceProvider;
use DataExporter\ExporterServiceProvider;
use Eloquent\ImageAble\ImageAbleServiceProvider;
use Eloquent\Meta\MetaServiceProvider;
use Illuminate\Support\ServiceProvider;
use Flysap\Support;
use Laravel\Meta\MetaSeoServiceProvider;
use Localization\LocaleServiceProvider;
use Parfumix\Imageonfly\ImageOnFlyServiceProvider;
use Translator\TranslatorServiceProvider;

class GeneratorServiceProvider extends ServiceProvider {

    /**
     * On boot's application load package requirements .
     */
    public function boot() {
        $this->loadRoutes()
            ->loadViews();

        $this->publishes([
            __DIR__.'/../configuration' => config_path('yaml/scaffold-generator'),
        ]);

        $this->registerMenu();
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
        $this->app->singleton('scaffold-manager', function () {
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

        Support\merge_yaml_config_from(
            config_path('yaml/scaffold-generator/general.yaml') , 'scaffold-generator'
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
     * Register menu .
     *
     */
    protected function registerMenu() {
        $namespaces = [
            storage_path(config('scaffold-generator.temp_path')),
            realpath(__DIR__ . '/../')
        ];

        $menuManager = app('menu-manager');

        array_walk($namespaces, function($namespace) use($menuManager) {
            $menuManager->addNamespace($namespace, true);
        });
    }

    /**
     * Register service provider dependencies .
     *
     */
    protected function registerPackageServices() {
        $providers = array(
            SluggableServiceProvider::class,
            MetaSeoServiceProvider::class,
            MetaServiceProvider::class,
            ImageAbleServiceProvider::class,
            ImageOnFlyServiceProvider::class,
            TagsServiceProvider::class,
            TranslatorServiceProvider::class,
            LocaleServiceProvider::class,
            ExporterServiceProvider::class
        );

        array_walk($providers, function($provider) {
            app()->register($provider);
        });
    }
}