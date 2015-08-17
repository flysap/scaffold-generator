<?php

namespace Flysap\ScaffoldGenerator\Generators;

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
class ModelGenerator extends Generator  {

    protected $templates = [
        'hasOne'         => 'public function {{function}}() { $this->hasOne("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'hasMany'        => 'public function {{function}}() { $this->hasMany("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'belongsToMany'  => 'public function {{function}}() { $this->belongsToMany("{{table}}", "{{foreign_key}}", "{{local_key}}"); }',
        'belongsTo'      => 'public function {{function}}() { $this->belongsTo("{{table}}", "{{local_key}}", "{{parent_key}}"); }',
    ];

    protected $defaultPackages = [
        'scaffold'
    ];

    /**
     * Prepare ..
     *
     * @param $contents
     * @return array
     */
    protected function prepare($contents) {
        $tables = [];

        array_walk($contents['tables'], function($table) use(& $tables) {
            $tableName          = $table['name'];

            if(! isset($tables[$tableName]))
                $tables[$tableName] = $table;

            if( isset($table['relations']) && !empty($table['relations']) ) {

                if(! is_array($table['relations']))
                    $this->setRelations($table['relations']);
                else
                    $this->setRawRelations($table['relations']);

                $relations = $this->getRelations();

                array_walk($relations, function($relation) use($tableName, & $tables) {

                    $tables[$relation['table']]['relations'] = [
                        'belongsTo' => [
                            [
                                'function'    => str_plural(strtolower($tableName)),
                                'table'       => str_singular(strtolower($tableName)),
                                'parent_key'  => $relation['reference'],
                                'local_key'   => $relation['foreign']
                            ]
                        ]
                    ];

                    $tables[$tableName]['relations'] = [
                        $relation['relation'] => [
                            [
                                'function'   => str_plural(strtolower($relation['table'])),
                                'table'      => str_singular(strtolower($relation['table'])),
                                'local_key'  => $relation['reference'],
                                'foreign_key'=> $relation['foreign']
                            ]
                        ]
                    ];
                });
            }

            if( ! is_array($table['fields']) )
                $this->setFields($table['fields']);
            else
                $this->setRawFields($table['fields']);

            $tables[$tableName]['fields'] = $this->getFields();
        });

        return ['tables' => $tables] + $contents;
    }

    /**
     * @return mixed
     */
    public function getStub() {
       return __DIR__ . DIRECTORY_SEPARATOR . '../../stubs/model.stub';
    }

    /**
     * Save all models .
     *
     * @param $path
     */
    public function save($path) {
        $contents = $this->getContents();

        array_walk($contents['tables'], function($table, $name) use($path) {

            $packages = $this->defaultPackages;

            if( isset($table['packages']) )
                $packages = array_merge($packages, array_keys($table['packages']));

            $packages = $this->getPackagesReplacement(
                $packages, ['path' => $path, 'class' => str_singular(ucfirst(strtolower($name)))]
            );

            $relationsString = '';
            $relationsArray  = [];
            if( isset($table['relations']) && !empty($table['relations']) ) {
                array_walk($table['relations'], function($relations, $key) use(& $relationsString, & $relationsArray) {
                    $template = array_get($this->templates, $key);

                    array_walk($relations, function($relation) use(& $relationsString, $template, & $relationsArray) {

                        $relationsArray[] = $relation['function'];
                        $relationsString .= $template;
                        array_walk($relation, function($value, $key)  use(& $relationsString, $template) {
                            $relationsString = str_replace('{{'.$key.'}}', $value, $relationsString);
                        });
                    });

                });
            }

            $fields = $this->getFieldsParser()
                ->setRawFields($table['fields'])
                ->getFieldsOnly("','", null, ["id"]);


            $this->setReplacement(
                [
                    'class'              => str_singular(ucfirst($name)),
                    'table_name'         => strtolower($name),
                    'table_fields'       => "'".$fields."'",
                    'table_relations'    => $relationsString,
                    'relations'          => 'protected $relations = [\'' . implode(',\'', $relationsArray) . '\'];',
                    'vendor'             => $this->getContent('vendor'),
                    'name'               => $this->getContent('name'),
                ] + $packages
            );

            parent::save($path . DIRECTORY_SEPARATOR . str_singular(ucfirst(strtolower($name))) . '.php');
        });
    }

    /**
     * Prepare package replacer .
     *
     * @param $packages
     * @param array $arguments
     * @return array
     */
    protected function getPackagesReplacement($packages, $arguments = array()) {
        $aliases = config('scaffold-generator.packages');

        $replacement = [];
        foreach($packages as $package => $options) {
            if(is_numeric($package))
                $package = $options;

            if( ! in_array($package, array_keys($aliases)) )
                return false;

            $class = $aliases[$package]['class'];

            if(! class_exists($class))
                return false;

            $data = (new $class($arguments))
                ->buildDepency()
                ->toArray();

            foreach ($data as $key => $value) {
                @$replacement[$key] .= $value;
            }
        }

        return $replacement;
    }
}