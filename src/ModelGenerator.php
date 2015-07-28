<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;

class ModelGenerator extends Generator {

    const STUB_PATH = 'stubs/model.stub';

    /**
     * @var
     */
    protected $tables;

    /**
     * @var array
     */
    protected $templates = [
        'hasOne'         => 'public function {{table}}() { $this->hasOne("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'hasMany'        => 'public function {{table}}() { $this->hasMany("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'belongsToMany'  => 'public function {{table}}() { $this->belongsToMany("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'belongsTo'      => 'public function {{table}}() { $this->belongsToMany("{{table}}", "{{local_key}}", "{{parent_key}}"); }',
    ];

    /**
     * Set tables .
     *
     * @param $tables
     * @return $this
     */
    public function setTables($tables) {
        array_walk($tables, function($table) {
            $this->tables[$table['name']] = $table;
        });

        $this->setUpRelations();

        return $this;
    }

    /**
     * Set up relations
     */
    protected function setUpRelations() {
        array_walk($this->tables, function($table, $key) {
            if( isset($table['relations']) && !empty($table['relations']) ) {

                $this->setRelations($table['relations']);
                $relations = $this->getRelations();

                array_walk($relations, function($relation) use($key) {

                    $this->tables[$relation['table']]['relations'] = [
                        $relation['relation'] => [
                            [
                                'table'       => str_singular(ucfirst(strtolower($key))),
                                'foreign_key' => $relation['foreign'],
                                'local_key'   => $relation['reference']
                            ]
                        ]
                    ];

                    $this->tables[$key]['relations'] = [
                        'belongsTo' => [
                            [
                                'table' => str_singular(ucfirst(strtolower($relation['table']))),
                                'local_key' => $relation['foreign'],
                                'parent_key' => $relation['reference']
                            ]
                        ]
                    ];


                });
            }
        });
    }

    /**
     * Prepare table .
     *
     * @param $name
     * @return StubGenerator
     * @throws StubException
     */
    public function prepare($name) {
        $this->stubGenerator
            ->loadStub(
                __DIR__ . DIRECTORY_SEPARATOR . '../' . self::STUB_PATH
            );

        $table = $this->tables[$name];

        $fields = $this->fieldParser
            ->setFields($table['fields'])
            ->getFieldsOnly("','", null, ["id"]);

        $this->setTable($table['name']);

        if( ! $this->getTable() )
            throw new StubException(_("Invalid table"));

        $relationsString = '';

        array_walk($table['relations'], function($relations, $key) use(& $relationsString) {
           $template = array_get($this->templates, $key);

            array_walk($relations, function($relation) use(& $relationsString, $template) {

                $relationsString .= $template;
                array_walk($relation, function($value, $key)  use(& $relationsString, $template) {
                    $relationsString = str_replace('{{'.$key.'}}', $value, $relationsString);
                });
            });
        });

        /** Generate models file . */
        $this->stubGenerator
            ->addFields([
                'class'           => str_singular(ucfirst($this->getTable())),
                'table_name'      => strtolower($this->getTable()),
                'table_fields'    => "'" . $fields . "'",
                'table_relations' => $relationsString,
            ]);

        return $this->stubGenerator;
    }

    /**
     * Save migration .
     *
     * @param $path
     */
    public function save($path) {
        array_walk($this->tables, function($table) use($path) {
            $this->prepare($table['name'])
                ->save($path . DIRECTORY_SEPARATOR . str_singular(ucfirst(strtolower($table['name']))) . '.php');
        });
    }
}