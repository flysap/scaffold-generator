<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Flysap\ScaffoldGenerator\Generator;

class RoutesGenerator extends Generator {

    /**
     * Class instance .
     *
     * @var
     */
    public static $instance;

    /**
     * @return ServiceProviderGenerator
     */
    public static function getInstance() {
        if( ! self::$instance )
            self::$instance = (new self);

        return self::$instance;
    }

    /**
     * Initialize .
     */
    public function init() {
        parent::init();

        $this->setStub(
            __DIR__ . DIRECTORY_SEPARATOR .'../../stubs/packages/routes.stub'
        );
    }

}