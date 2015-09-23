<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Flysap\ScaffoldGenerator\Generator;

class MigrationGenerator extends Generator {

    /**
     * Aliases from sql to BluePrint functions .
     *
     * @var array
     */
    protected $typeAlias = [
        'increments' => 'increments',
        'inc' => 'increments',
        'int' => 'integer',
        'tinyint' => 'tinyInteger',
        'smallint' => 'smallInteger',
        'mediumint' => 'mediumInteger',
        'bigint' => 'bigInteger',
        'float' => 'float',
        'double' => 'double',

        'char' => 'char',
        'varchar' => 'string',
        'mediumtext' => 'mediumText',
        'longtext' => 'longText',
        'text' => 'text',

        'enum' => 'enum',

        'date' => 'date',
        'datetime' => 'dateTime',
        'time' => 'time',
    ];

    protected $specialValues = [
        'unsigned' => '->unsigned()',
        'index' => '->index()',
        'nullable' => '->nullable()',
    ];

    /**
     * @var string
     */
    protected $fieldTemplate = "\$table->{type}('{name}')";

    /**
     * @var string
     */
    protected $fieldRelation = "\$table->foreign('{foreign}')->references('{reference}')->on('{table}')->onDelete('{on_delete}')->onUpdate('{on_update}');";

    public function init() {
        parent::init();

        $this->setStub(
            __DIR__ . DIRECTORY_SEPARATOR . '../../stubs/migration_create.stub'
        );

        $this->setFormatter(function($replacements, $time) {
             return [
                'class_name'        => 'Create' . $replacements['class_name'] . 'Table',
                'table_name'        => strtolower($replacements['table_name']),
                'migration_name'    => date('Y_m_d_His', time() + $time) . '_create_' . strtolower($replacements['migration_name']) . '_table.php',
            ] + $replacements;
        });
    }

    /**
     * Save all models ..
     *
     * @param $path
     * @return mixed|void
     */
    public function save($path) {
        $contents = $this->getContents();

        foreach ($contents as $table) {
            $this->clearReplacements();

            $className = ucfirst(str_plural($table['name']));

            $fields = $this->buildFields(
                $table['fields']
            );

            $relations = $this->buildRelations(
                $table['relations']
            );

            $replacements = $this->formatReplacements([
                'class_name'        => $className,
                'table_name'        => $className,
                'table_fields'      => $fields,
                'table_relations'   => $relations,
                'migration_name'    => $className,
            ]);

            $this->addReplacement(array_except($replacements, 'migration_name'));

            parent::save($path . DIRECTORY_SEPARATOR . 'migrations' . DIRECTORY_SEPARATOR . $replacements['migration_name']);
        }

        return $this;
    }


    /**
     * Build relations .
     *
     * @param array $relations
     * @return array
     */
    protected function buildRelations($relations) {
        $this->setRelations($relations);

        $tableRelations = [];
        $relations = array_filter($this->getRelations());
        array_walk($relations, function ($relation) use (& $tableRelations) {
            $string = $this->fieldRelation;
            foreach ($relation as $key => $value) {
                $string = str_replace('{' . $key . '}', $value, $string);
            }

            $tableRelations[] = $string . ';';
        });

        return $tableRelations;
    }

    /**
     * Build fields .
     *
     * @param array $fields
     * @return array
     */
    protected function buildFields($fields) {
        $tableFields = [];
        $fields = $this->setFields($fields)->getFields();

        array_walk($fields, function ($field) use (& $tableFields) {

            $string = $this->fieldTemplate;

            foreach ($field as $key => $value) {
                if ($key == 'type') {
                    if (isset($this->typeAlias[$value]))
                        $value = $this->typeAlias[$value];
                }

                $string = str_replace('{' . $key . '}', $value, $string);
            }

            foreach ($this->specialValues as $key => $value) {
                if (isset($field[$key]))
                    $string .= $value;
            }

            $tableFields[] = $string . ';';
        });

        return $tableFields;
    }
}
