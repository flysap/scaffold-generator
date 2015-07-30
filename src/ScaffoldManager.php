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

    /**
     * @var ModelGenerator
     */
    private $modelGenerator;

    public function __construct(StubGenerator $stubGenerator, MigrationGenerator $migrationGenerator, ModelGenerator $modelGenerator) {

        $this->stubGenerator = $stubGenerator;
        $this->migrationGenerator = $migrationGenerator;
        $this->modelGenerator = $modelGenerator;
    }

    /**
     * Generate scaffold .
     *
     * @param $post
     * @return string
     */
    public function generate($post) {
        try {
            $path = DIRECTORY_SEPARATOR . $post['vendor'] . DIRECTORY_SEPARATOR . $post['name'];

            /** Generate the module.json file. */
            $this->stubGenerator
                ->loadStub( StubGenerator::getStubPath('modules') )
                ->addFields(array_only($post, ['name', 'vendor', 'description', 'version']))
                ->save($path . DIRECTORY_SEPARATOR . 'module.json');


            /** Generate model files . */
            $this->modelGenerator
                ->setTables($post['tables'])
                ->save($path);


            array_walk($post['tables'], function($table) use($path) {

                $this->migrationGenerator
                    ->setTable($table['name'])
                    ->setFields($table['fields'])
                    ->setRelations($table['relations'])
                    ->save($path . DIRECTORY_SEPARATOR . 'migrations/add_' . strtolower(str_singular($table['name'])) . '_migration.php');
            });

            return 'storage/' . config('scaffold-generator.temp_path') . $path;

        } catch(StubException $e) {

        }
    }

    /**
     * Flush all modules .
     */
    public function flushModules() {
        if( \Flysap\Support\is_path_exists(
            storage_path(
                config('scaffold-generator.temp_path')
            )
        ) )
            \Flysap\Support\remove_paths(
                storage_path(
                    config('scaffold-generator.temp_path')
                )
            );

        return $this;
    }

    /**
     * Flush module .
     * @param $module
     * @return $this
     */
    public function flushModule($module) {
        if( \Flysap\Support\is_path_exists(
            storage_path(
                config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $module
            )
        ) )
            \Flysap\Support\remove_paths(
                storage_path(
                    config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $module
                )
            );

        return $this;
    }
}