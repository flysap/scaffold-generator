<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;

class ScaffoldManager {

    /**
     * @var StubGenerator
     */
    private $stubGenerator;

    /**
     * @var MigrationGenerator
     */
    private $migrationGenerator;

    public function __construct(StubGenerator $stubGenerator, MigrationGenerator $migrationGenerator) {

        $this->stubGenerator = $stubGenerator;
        $this->migrationGenerator = $migrationGenerator;
    }

    /**
     * Generate scaffold .
     *
     * @param $post
     */
    public function generate($post) {
        try {
            $fullPath = DIRECTORY_SEPARATOR . $post['vendor'] . DIRECTORY_SEPARATOR . $post['name'];

            /** Generate the module.json file. */
            $this->stubGenerator
                ->loadStub( $this->getStubPath('modules') )
                ->addFields(array_only($post, ['name', 'vendor', 'description', 'version']))
                ->save($fullPath . DIRECTORY_SEPARATOR . 'module.json');

            /** Save table and relations .. */
            array_walk($post['tables'], function($table) use($fullPath) {

                $tableName  = strtolower(str_singular($table['name']));
                $path       = $fullPath . DIRECTORY_SEPARATOR;


                /** Generate migration files . */
                $this->migrationGenerator
                    ->setTable($table['name'])
                    ->setFields($table['fields'])
                    ->save($path . DIRECTORY_SEPARATOR . 'migrations/add_' . $tableName . '_migration.php');


                /** Generate models file . */
                $this->stubGenerator
                    ->loadStub( $this->getStubPath('Model') )
                    ->addFields([
                        'class'        => ucfirst($tableName),
                        'table_name'   => strtolower($table['name']),
                        'table_fields' => $table['fields'],
                    ])
                    ->save($path . ucfirst($tableName) . '.php');

            });


        } catch(StubException $e) {

        }
    }

    protected function prepareFields($fields) {
        #@todo convert the fields from string to array ..
    }

    protected function migrate() {
        #@todo migrate into sqlite database preparete table and fields
    }

    /**
     * Return stub path .
     *
     * @param null $stub
     * @return string
     */
    protected function getStubPath($stub = null) {
        return __DIR__ . '/../stubs/' . (! is_null($stub) ? $stub . '.stub' : '');
    }
}