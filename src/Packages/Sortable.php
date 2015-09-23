<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\Generator;
use Flysap\ScaffoldGenerator\Generators\MigrationGenerator;
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
        (new MigrationGenerator)
            ->setStub(__DIR__ . '/../../stubs/migration_update.stub')
            ->setFormatter(function($replacements, $time) {
                return [
                    'class_name'        => 'AddPosition' . $replacements['class_name'] . 'Table',
                    'table_name'        => strtolower($replacements['table_name']),
                    'migration_name'    => date('Y_m_d_His', time() + $time) . '_add_position_' . strtolower($replacements['migration_name']) . '_table.php',
                ] + $replacements;
            })
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