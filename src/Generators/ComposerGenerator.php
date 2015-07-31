<?php

namespace Flysap\ScaffoldGenerator\Generators;

class ComposerGenerator extends Generator {

    /**
     * @return mixed
     */
    function getStub() {
        return __DIR__ . DIRECTORY_SEPARATOR .'../../stubs/composer.stub';
    }
}