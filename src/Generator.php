<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Generators\ComposerGenerator;
use Flysap\ScaffoldGenerator\Generators\ConfigGenerator;
use Flysap\ScaffoldGenerator\Generators\MigrationGenerator;
use Flysap\ScaffoldGenerator\Generators\ModelGenerator;
use Symfony\Component\Filesystem\Filesystem;

class Generator {

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
           app('stub-generator'), app('field-parser'), app('relation-parser')
        ));
    }
}