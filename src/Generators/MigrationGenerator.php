<?php

namespace Flysap\ScaffoldGenerator\Generators;

class MigrationGenerator extends Generator {

    /**
     * Aliases from sql to BluePrint functions .
     *
     * @var array
     */
    protected $typeAlias = [
        'increments'  => 'increments',
        'int'         => 'integer',
        'tinyint'     => 'tinyInteger',
        'smallint'    => 'smallInteger',
        'mediumint'   => 'mediumInteger',
        'bigint'      => 'bigInteger',
        'float'       => 'float',
        'double'      => 'double',

        'char'        => 'char',
        'varchar'     => 'string',
        'mediumtext'  => 'mediumText',
        'longtext'    => 'longText',
        'text'        => 'text',

        'enum'        => 'enum',

        'date'        => 'date',
        'datetime'    => 'dateTime',
        'time'        => 'time',
    ];

    protected $specialValues = [
        'unsigned' => '->unsigned()',
        'index'    => '->index()',
        'nullable' => '->nullable()',
    ];

    /**
     * @var string
     */
    protected $fieldTemplate = "\$table->{type}('{name}')";

    /**
     * @var string
     */
    protected $fieldRelation = "\$table->foreign('{foreign}')->references('{reference}')->on('{table}')->onDelete('{on_delete}')->onUpdate('{on_update}')";

    /**
     * Save all models ..
     *
     * @param $path
     * @return mixed|void
     */
    public function save($path) {
        $contents = $this->getContents();

        $t = 10;
        array_walk($contents, function($table) use($path, & $t)  {

            $tableFields = [];
            $fields      = $this->setFields(
                $table['fields']
            )->getFields();


            $t += 100;
            array_walk($fields, function($field) use(& $tableFields) {

                $string = $this->fieldTemplate;

                foreach ($field as $key => $value) {
                    if( $key == 'type' ) {
                        if( isset($this->typeAlias[$value]) )
                            $value = $this->typeAlias[$value];
                    }

                    $string = str_replace('{'.$key.'}', $value, $string);
                }


                foreach ($this->specialValues as $key => $value) {
                    if( isset($field[$key]) )
                        $string .= $value;
                }

                $tableFields[] = $string . ';';
            });


            $tableRelations = [];
            if( isset($table['relations']) ) {
                $this->setRelations($table['relations']);

                $tableRelations = [];
                $relations      = array_filter($this->getRelations());
                array_walk($relations, function($relation) use(& $tableRelations) {
                    $string = $this->fieldRelation;
                    foreach ($relation as $key => $value) {
                        $string = str_replace('{'.$key.'}', $value, $string);
                    }

                    $tableRelations[] = $string . ';';
                });
            }


            $this->setReplacements([
                    'class_name'      => 'Create'.ucfirst(str_plural($table['name'])).'Table',
                    'table_name'      => str_plural($table['name']),
                    'table_fields'    => $tableFields,
                    'table_relations' => $tableRelations,
                ]);


            $time = date('Y_m_d_His', time() + $t);

            parent::save($path . DIRECTORY_SEPARATOR . 'migrations/'.$time.'_create_' . strtolower(str_plural($table['name'])) . '_table.php');
        });
    }

    /**
     * @return mixed
     */
    function getStub() {
        return __DIR__ . DIRECTORY_SEPARATOR .'../../stubs/migration.stub';
    }
}
