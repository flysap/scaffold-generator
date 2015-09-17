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
                                'table'       => str_plural(strtolower($tableName)),
                                'parent_key'  => $relation['reference'],
                                'local_key'   => $relation['foreign']
                            ]
                        ]
                    ];

                    $tables[$tableName]['relations'] = [
                        $relation['relation'] => [
                            [
                                'function'   => str_plural(strtolower($relation['table'])),
                                'table'      => str_plural(strtolower($relation['table'])),
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
     * @return mixed|void
     */
    public function save($path) {
        $tables = $this->getContent('tables');

        foreach($tables as $tableName => $options) {

            /** @var Get the full list of installed packages .. $packages */
            $packages = $this->mergeWithDefaultPackages(
                isset($table['packages']) ? $table['packages'] : []
            );

            /** @var Get package replacement . $packageReplacement */
            $packageReplacement = $this->buildPackagesAssets(
                $packages,
                ['path' => $path, 'class' => str_singular(ucfirst(strtolower($tableName)))]
            );

            foreach($packageReplacement as $package => $replacements)
                $this->addReplacement($replacements);

            /** @var Build relation replacements . $relationReplacement */
            $relationReplacement = $this->buildRelations(
                isset($options['relations']) ? (array)$options['relations'] : []
            );


            $fields = $this->getFieldsParser()
                ->setRawFields($options['fields'])
                ->getFieldsOnly("','", null, ["id"]);


            $this->addReplacement([
                'class'              => str_singular(ucfirst($tableName)),
                'table_name'         => strtolower($tableName),
                'table_fields'       => "'".$fields."'",
                'table_relations'    => $relationReplacement['string'],
                'relations'          => 'protected $relation = [\'' . implode(',\'', $relationReplacement['array']) . '\'];',
                'vendor'             => $this->getContent('vendor'),
                'name'               => $this->getContent('name'),
            ]);

            parent::save($path . DIRECTORY_SEPARATOR . str_singular(ucfirst(strtolower($tableName))) . '.php');
        }


    }

    /**
     * Build relations .
     *
     * @param array $relations
     * @return string
     */
    protected function buildRelations(array $relations = array()) {
        $relationsString = '';
        $relationsArray  = [];

        foreach ($relations as $relation => $options) {
            $template = array_get($this->templates, $relation);

            array_walk($relations, function($relation) use(& $relationsString, $template, & $relationsArray) {

                $relationsArray[] = $relation['function'];
                $relationsString .= $template;
                array_walk($relation, function($value, $key)  use(& $relationsString, $template) {
                    $relationsString = str_replace('{{'.$key.'}}', $value, $relationsString);
                });
            });
        }

        return [
            'string' => $relationsString,
            'array'  => $relationsArray,
        ];
    }

    /**
     * Build packages assets  and get replacement .
     *
     * @param array $packages
     * @return array
     */
    protected function buildPackagesAssets(array $packages = array()) {

        $replacements = [];

        foreach($packages as $package => $attributes) {

            if( ! $this->packageManager->hasPackage($package) )
                continue;

            $packageInstance = $this->packageManager
                ->packageInstance($package, $attributes);

            $replacement = $packageInstance
                ->buildDependency()
                ->toArray();

            $replacements[$package] = $replacement;
        }

        return $replacements;
    }

    /**
     * Merge current packages with default packages .
     *
     * @param array $packages
     * @return array
     */
    protected function mergeWithDefaultPackages(array $packages = array()) {
        $packages = array_merge(
            $packages,
            $this->getPackageManager()->getDefaultPackages()
        );

        return $packages;
    }

}