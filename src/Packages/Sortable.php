<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\Generator;
use Flysap\ScaffoldGenerator\PackageAble;

class Sortable extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use SortableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', Sortable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Eloquent\\Sortable\\Sortable;\nuse Eloquent\\Sortable\\SortableTrait;\n";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        $generator = app('generator');

        $generator->generate(
            Generator::GENERATOR_MIGRATION
        )
            ->setStub(__DIR__ . '/../../stubs/migration_update.stub')
            ->setContents([
                [
                    'name'      => $this->getAttribute('name'),
                    'fields'    => 'position:integer',
                    'relations' => '',
                ]
            ])
            ->save(
                $this->getAttribute('path')
            );


        return $this;
    }
}