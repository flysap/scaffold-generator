<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\Generators\MigrationGenerator;
use Flysap\ScaffoldGenerator\Generators\ModelGenerator;
use Flysap\ScaffoldGenerator\PackageAble;
use Flysap\Support;

class TranslatAble extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use TranslatableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', Translatable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Eloquent\\Translatable\\Translatable;\nuse Eloquent\\Translatable\\TranslatableTrait;\n";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        Support\artisan('vendor:publish', [
            '--provider' => 'Localization\LocaleServiceProvider'
        ]);

        Support\artisan('vendor:publish', [
            '--provider' => 'Translator\TranslatorServiceProvider'
        ]);

        $attribute = $this->getAttribute('attributes');

        $jsonArray = [];
        if( preg_match("/(?s)(\\[.*\\])/", $attribute, $matches) )
            $jsonArray = eval("return ".$matches[0].";");

        $fields = implode(':string, ', $jsonArray) . ':string';

        $relationsFields = [
            str_singular($this->getAttribute('name')) . '_id',
            'language_id'
        ];

        foreach ($relationsFields as $field) {
            $fields .= ', '. $field .':int(11)|unsigned';
        }

        /**
         * Create migration ..
         */
        (new MigrationGenerator)
            ->setStub(__DIR__ . '/../../stubs/migration_create.stub')
            ->setFormatter(function(array $replacements, $time) {
                return [
                    'class_name'        => 'CreateTranslations' . $replacements['class_name'] . 'Table',
                    'table_name'        => strtolower($replacements['table_name']) .'_translations',
                    'migration_name'    => date('Y_m_d_His', time() + $time) . '_create_translations_' . strtolower($replacements['migration_name']) . '_table.php',
                ] + $replacements;
            })
            ->setContents([
                [
                    'name'      => $this->getAttribute('name'),
                    'fields'    => $fields,
                    'relations' => $relationsFields[0] . ':id|'.   $this->getAttribute('name'). ', '. $relationsFields[1].':id|languages',
                ]
            ])
            ->save(
                $this->getAttribute('path')
            );

        /**
         * Generate model translations .
         */
        (new ModelGenerator)
            ->setFormatter(function(array $replacements, $time) {
                $class = ucfirst(str_singular($this->getAttribute('name'))) . 'Translations';

                return [
                    'class' => $class,
                    'table_name' => strtolower(str_singular($this->getAttribute('name'))) . '_translations',
                    'options' => $class
                ] + $replacements;
            })
            ->setContents(['vendor' => $this->getAttribute('module')['vendor'], 'name' => $this->getAttribute('module')['name'], 'tables' => [
                [
                    'name'   => $this->getAttribute('name') .'Translations',
                    'fields' => $fields,
                    'relations' => '',
                ]
            ]])
            ->save(
                $this->getAttribute('path')
            );

        return $this;
    }
}