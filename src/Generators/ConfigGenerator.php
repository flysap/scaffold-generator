<?php

namespace Flysap\ScaffoldGenerator\Generators;

class ConfigGenerator extends Generator {

    /**
     * @return mixed
     */
    function getStub() {
        return __DIR__ . DIRECTORY_SEPARATOR .'../../stubs/modules.stub';
    }
}