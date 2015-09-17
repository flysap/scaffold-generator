<?php

namespace Flysap\ScaffoldGenerator\Generators;

class ComposerGenerator extends Generator {

    public function init() {
        parent::init();

        $this->setStub(
            __DIR__ . DIRECTORY_SEPARATOR .'../../stubs/composer.stub'
        );
    }
}