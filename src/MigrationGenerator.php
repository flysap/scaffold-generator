<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Flysap\ScaffoldGenerator\Parsers\Field;
use Flysap\ScaffoldGenerator\Parsers\Relation;
use Symfony\Component\Filesystem\Filesystem;

class MigrationGenerator extends Generator {

    const STUB_PATH = 'stubs/migration.stub';

    /**
     * Aliases from sql to BluePrint functions .
     *
     * @var array
     */
    protected $typeAlias = [
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
    protected $fieldTemplate = '$table->{type}("{name}", "{value}")';

    /**
     * @var string
     */
    protected $fieldRelation = '$table->foreign("{foreign}")->references("{reference}")->on("{on}")->onDelete("{on_delete}")->onUpdate("{on_update}")';


    /**
     * Prepare to migration .
     *
     * @throws StubException
     */
    public function prepare() {
        if( ! $this->getFields() )
            throw new StubException(_("Invalid fields"));

        if( ! $this->getTable() )
            throw new StubException(_("Invalid table"));

        /** @var Prepare table fields .. $tableFields */
        $tableFields = [];
        $fields      = $this->getFields();
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

        /** Prepare table relations .. */
        $tableRelations = [];
        $relations      = array_filter($this->getRelations());
        array_walk($relations, function($relation) use(& $tableRelations) {
            $string = $this->fieldRelation;
            foreach ($relation as $key => $value) {
                $string = str_replace('{'.$key.'}', $value, $string);
            }

            $tableRelations[] = $string . ';';
        });

        $this->stubGenerator
            ->loadStub(
                __DIR__ . DIRECTORY_SEPARATOR . '../' . self::STUB_PATH
            );

        $this->stubGenerator
            ->addFields([
                'table_name'      => $this->getTable(),
                'table_fields'    => $tableFields,
                'table_relations' => $tableRelations,
            ]);

        return $this->stubGenerator;
    }

}