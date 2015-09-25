<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\Generators\ControllerGenerator;
use Flysap\ScaffoldGenerator\Generators\RoutesGenerator;
use Flysap\ScaffoldGenerator\PackageAble;

class ApiAble extends Package implements PackageAble {

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        $class  = strtolower(str_singular($this->getAttribute('name')));
        $vendor = ucfirst($this->getAttribute('module')['vendor']);
        $name   = ucfirst($this->getAttribute('module')['name']);

        /**
         * Generate api routes for controller .
         */
        $routesGenerator = RoutesGenerator::getInstance();
        $routesGenerator
            ->addReplacement([
                'route' => 'Route::resource('.str_plural($class).', '.'Modules\\'. ucfirst($vendor) . '\\' . $name . '\\controllers\\' . ucfirst($class) . 'ApiController'.');',
            ]);

        /**
         * Generate controller .
         */
        (new ControllerGenerator)
            ->addReplacement([
                'namespace' => 'Modules\\' . $vendor . '\\' . $name . '\\controllers;',
                'index_action' => ' ',
                'create_action' => ' ',
                'store_action' => ' ',
                'show_action' => ' ',
                'edit_action' => ' ',
                'update_action' => ' ',
                'destroy_action' => ' ',
            ])
            ->save(
                $this->getAttribute('path') . DIRECTORY_SEPARATOR . 'controllers' . DIRECTORY_SEPARATOR . ucfirst($class) .'ApiController.php'
            );

        return $this;
    }
}