<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;

/**
 * When post is incoming need to validate each field
 *
 * Possible formats ..
 *   1.
 *      a. fields    - id:int(11)|unsigned, name:string(55), etc ..
 *      b. relations - user_id:id|users|cascade|cascade|hasMany
 *
 *   2.
 *    tables[] ->
 *      a. name
 *      b. fields:
 *           id:
 *            - type => int
 *            - length => 11
 *            - unsigned => unsigned
 *            - index => index
 *           etc:
 *
 *      c. relations:
 *            -
 *              - foreign => foreign
 *              - reference => reference
 *              - table => table
 *              - on_update => on_update
 *              - on_delete => on_delete
 *              - relation => relation
 *      d. packages
 *          - package
 *             - options
 *
 * Class ModelGenerator
 * @package Flysap\ScaffoldGenerator
 */
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
        'hasOne'         => 'public function {{function}}() { $this->hasOne("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'hasMany'        => 'public function {{function}}() { $this->hasMany("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'belongsToMany'  => 'public function {{function}}() { $this->belongsToMany("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'belongsTo'      => 'public function {{function}}() { $this->belongsToMany("{{table}}", "{{local_key}}", "{{parent_key}}"); }',
    ];

    /**
     * @var
     */
    protected $aliases;

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

        $this->prepareRelations();

        return $this;
    }

    /**
     * Set up relations
     */
    protected function prepareRelations() {
        array_walk($this->tables, function($table, $key) {
            if( isset($table['relations']) && !empty($table['relations']) ) {

                $this->setRawRelations($table['relations']);
                $relations = $this->getRelations();

                array_walk($relations, function($relation) use($key) {

                    $this->tables[$relation['table']]['relations'] = [
                        $relation['relation'] => [
                            [
                                'function'    => str_singular(strtolower($key)),
                                'table'       => str_singular(strtolower($key)),
                                'foreign_key' => $relation['foreign'],
                                'local_key'   => $relation['reference']
                            ]
                        ]
                    ];

                    $this->tables[$key]['relations'] = [
                        'belongsTo' => [
                            [
                                'function'   => str_singular(strtolower($relation['table'])),
                                'table'      => str_singular(strtolower($relation['table'])),
                                'local_key'  => $relation['foreign'],
                                'parent_key' => $relation['reference']
                            ]
                        ]
                    ];
                });
            }
        });
    }

    /**
     * Prepare package replacer .
     *
     * @param $packages
     * @return array
     */
    protected function getPackagesReplacement($packages) {
        $aliases = $this->getPackageAliases();

        $replacement = [];
        array_walk($packages, function($options, $alias) use($aliases, & $replacement) {
            if( ! in_array($alias, array_keys($aliases)) )
                return false;

            $class = $aliases[$alias];

            if(! class_exists($class))
                return false;

            $replacement = array_merge($replacement, (new $class($options))
                ->toArray());
        });

        return $replacement;
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

        $packages = [
            'packages_traits'    => '',
            'packages_options'   => '',
            'packages_contracts' => '',
            'packages_import'    => '',
        ];

        if( isset($table['packages']) )
            $packages = $this->getPackagesReplacement(
                $table['packages']
            );

        $relationsString = '';

        if( isset($table['relations']) && !empty($table['relations']) )
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
                'class'              => str_singular(ucfirst($this->getTable())),
                'table_name'         => strtolower($this->getTable()),
                'table_fields'       => "'" . $fields . "'",
                'table_relations'    => $relationsString,
            ] + $packages);

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

    /**
     * Return package aliases .
     *
     * @return mixed
     */
    protected function getPackageAliases() {
        if( ! $this->aliases )
            $this->aliases = config('scaffold-generator.package_alias');

        return $this->aliases;
    }
}