<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Flysap\ScaffoldGenerator\Generator;

class ServiceProviderGenerator extends Generator {

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