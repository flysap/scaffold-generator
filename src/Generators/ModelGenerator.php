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

use Flysap\ScaffoldGenerator\Generator;

class ModelGenerator extends Generator  {

    protected $templates = [
        'hasOne'         => "public function {{function}}() { \$this->hasOne('{{table}}', '{{foreign_key}}', '{{local_key}}'); }\n",
        'hasMany'        => "public function {{function}}() { \$this->hasMany('{{table}}', '{{foreign_key}}', '{{local_key}}'); }\n",
        'belongsToMany'  => "public function {{function}}() { \$this->belongsToMany('{{table}}', '{{foreign_key}}', '{{local_key}}'); }",
        'belongsTo'      => "public function {{function}}() { \$this->belongsTo('{{table}}', '{{local_key}}', '{{parent_key}}'); }",
    ];

    public function init() {
        parent::init();

        $this->setStub(__DIR__ . DIRECTORY_SEPARATOR . '../../stubs/model.stub');
    }

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

                    if(! isset( $tables[$relation['table']] ['relations'] ['belongsTo'] ))
                        $tables [$relation['table']] ['relations'] = ['belongsTo' => []];

                    if(! isset($tables [$tableName] ['relations'] ['hasMany'] ))
                        $tables [$tableName] ['relations']  = ['hasMany' => []];

                    $tables[$relation['table']]['relations']['belongsTo'][] = [
                        'function'    => str_plural(strtolower($tableName)),
                        'table'       => str_plural(strtolower($tableName)),
                        'parent_key'  => $relation['reference'],
                        'local_key'   => $relation['foreign']
                    ];

                    $tables[$tableName]['relations']['hasMany'][] = [
                        'function'   => str_plural(strtolower($relation['table'])),
                        'table'      => str_plural(strtolower($relation['table'])),
                        'local_key'  => $relation['reference'],
                        'foreign_key'=> $relation['foreign']
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
     * Save all models .
     *
     * @param $path
     * @return mixed|void
     */
    public function save($path) {
        $tables = $this->getContent('tables');

        foreach($tables as $tableName => $options) {
            $this->clearReplacements();

            $class = str_singular(ucfirst($tableName));
            $table = strtolower(str_plural($tableName));

            /** @var Get the full list of installed packages .. $packages */
            $packages = $this->mergeWithDefaultPackages(
                isset($options['packages']) ? $options['packages'] : []
            );

            /** @var Get package replacement . $packageReplacement */
            $packageReplacement = $this->buildPackagesAssets(
                $packages, array_merge(['path' => $path], $options)
            );

            $this->addReplacement($packageReplacement);

            /** @var Build relation replacements . $relationReplacement */
            $relationReplacement = $this->buildRelations(
                isset($options['relations']) ? is_array($options['relations']) ? $options['relations'] : [] : []
            );

            $fields = $this->getFieldsParser()
                ->setRawFields($options['fields'])
                ->getFieldsOnly("','", null, ["id"]);

            $this->addReplacement([
                'class'              => $class,
                'table_name'         => $table,
                'table_fields'       => "'".$fields."'",
                'table_relations'    => $relationReplacement['relations'],
                'relations'          => $relationReplacement['attributes'],
                'vendor'             => $this->getContent('vendor'),
                'name'               => $this->getContent('name'),
            ]);

            parent::save($path . DIRECTORY_SEPARATOR . $class . '.php');
        }
    }

    /**
     * Build relations .
     *
     * @param array $relations
     * @return string
     */
    protected function buildRelations(array $relations = array()) {
        $replacer = ''; $array = [];

        foreach($relations as $relation => $values) {
            if( isset($this->templates[$relation]) ) {

                $template = $this->templates[$relation];

                foreach ($values as $key => $value) {
                    $replacer .= $template;
                    $array[]   = $value['function'];

                    foreach ($value as $k => $v)
                        $replacer = str_replace('{{'.$k.'}}', $v, $replacer);
                }
            }
        }

        $attributes = '';
        foreach($array as $k => $relation)
            $attributes .= sprintf("%s'%s'", $k ? ',' : '',$relation);

        $attributes = 'protected $relation = ['.$attributes.'];';

        return [
            'relations' => $replacer,
            'attributes'=> $attributes,
        ];
    }

    /**
     * Build packages assets  and get replacement .
     *
     * @param array $packages
     * @param array $options
     * @return array
     */
    protected function buildPackagesAssets(array $packages = array(), array $options = array()) {

        $replacements = [];

        foreach($packages as $package => $attributes) {

            if( ! $this->packageManager->hasPackage($package) )
                continue;

            if(!is_array($attributes))
                $attributes = (array)$attributes;

            $packageInstance = $this->packageManager
                ->packageInstance($package, array_merge($attributes, $options));

            $replacement = $packageInstance
                ->buildDependency()
                ->toArray();

            foreach ($replacement as $key => $value)
                @$replacements[$key] .= $value;
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
            $this->getPackageManager()->getDefaultPackages(),
            $packages
        );

        return $packages;
    }

}