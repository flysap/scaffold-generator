<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\Generator;
use Flysap\ScaffoldGenerator\Generators\MigrationGenerator;
use Flysap\ScaffoldGenerator\PackageAble;

class Sluggable extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use SluggableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', SluggableInterface';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Cviebrock\\EloquentSluggable\\SluggableInterface;\nuse Cviebrock\\EloquentSluggable\\SluggableTrait;\n";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        $attribute = $this->getAttribute('attributes');

        $slug = 'slug';
        if( preg_match("/(?s)(\\[.*\\])/", $attribute, $matches) ) {
            #@todo temp ??
            $jsonArray = eval("return ".$matches[0].";");
            $slug      = isset($jsonArray['save_to']) ? $jsonArray['save_to'] : $slug;
        }


        (new MigrationGenerator)
            ->setStub(__DIR__ . '/../../stubs/migration_update.stub')
            ->setFormatter(function(array $replacements, $time) {
                return [
                    'class_name'        => 'AddSlug' . $replacements['class_name'] . 'Table',
                    'table_name'        => strtolower($replacements['table_name']),
                    'migration_name'    => date('Y_m_d_His', time() + $time) . '_add_slug_' . strtolower($replacements['migration_name']) . '_table.php',
                ] + $replacements;
            })
            ->setContents([
                [
                    'name'      => $this->getAttribute('name'),
                    'fields'    => $slug.':string',
                    'relations' => '',
                ]
            ])
            ->save(
                $this->getAttribute('path')
            );


        return $this;
    }
}