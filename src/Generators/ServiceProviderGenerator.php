<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Flysap\ScaffoldGenerator\Generator;

class ServiceProviderGenerator extends Generator {

    /**
     * Class instance .
     *
     * @var
     */
    public static $instance;

    /**
     * Implement singleton pattern . There is need use that because :
     *
     *  a. most of packages need to add there own configurations
     *  b. apiAble package will be for different models .
     *
     * @return ServiceProviderGenerator
     */
    public static function getInstance() {
        if( ! self::$instance )
            self::$instance = (new self);

        return self::$instance;
    }

    /**
     * Initialize service generator .
     */
    public function init() {
        parent::init();

        $this->setStub(
            __DIR__ . DIRECTORY_SEPARATOR .'../../stubs/packages/service.stub'
        );
    }
}