<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Symfony\Component\Filesystem\Filesystem;

class ContextGenerator {

    const GENERATOR_MODEL     = ModelGenerator::class;
    const GENERATOR_CONFIG    = ConfigGenerator::class;
    const GENERATOR_COMPOSER  = ComposerGenerator::class;
    const GENERATOR_MIGRATION = MigrationGenerator::class;

    /**
     * @param $generator
     * @return mixed
     */
    public static function generate($generator = self::GENERATOR_MODEL) {
        return (new $generator(
            new Filesystem(), app('stub-generator'), app('field-parser'), app('relation-parser')
        ));
    }
}