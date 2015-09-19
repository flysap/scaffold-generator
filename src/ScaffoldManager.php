<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\ExportException;
use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Flysap\ScaffoldGenerator\Generators\ComposerGenerator;
use Flysap\ScaffoldGenerator\Generators\ConfigGenerator;
use Flysap\ScaffoldGenerator\Generators\MigrationGenerator;
use Flysap\ScaffoldGenerator\Generators\ModelGenerator;
use Flysap\Support;
use DB;

class ScaffoldManager {

    public function generate($params) {
        try {
            $module = $params['vendor'] . DIRECTORY_SEPARATOR . $params['name'];

            $this->flushModule($module);

            $path = DIRECTORY_SEPARATOR . $module;

            $tables    = $params['tables'];

            (new MigrationGenerator)
                ->setContents($tables)
                ->save(DIRECTORY_SEPARATOR . $module);


            (new ModelGenerator)
                ->setContents($params)
                ->save($path);


            (new ComposerGenerator)
                ->addReplacement(array_only($params, ['name', 'vendor', 'description', 'version']))
                ->save($path . DIRECTORY_SEPARATOR . 'composer.json');

            (new ConfigGenerator)
                ->setContents($params)
                ->save($path . DIRECTORY_SEPARATOR . 'module.json');

            $this->fixer(storage_path(
                config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $path
            ));

            $path = 'storage/' . config('scaffold-generator.temp_path') . $path;

            $this->flushDatabase('development', $path . DIRECTORY_SEPARATOR . 'migrations');

            return $path;

        } catch(StubException $e) {

        }
    }

    /**
     * Flush all modules .
     */
    public function flushModules() {
        if( Support\is_path_exists(
            storage_path(
                config('scaffold-generator.temp_path')
            )
        ) )
            Support\remove_paths(
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
        $module = realpath($module);

        if( Support\is_path_exists(
            storage_path(
                config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $module
            )
        ) )
            Support\remove_paths(
                storage_path(
                    config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $module
                )
            );

        return $this;
    }

    /**
     * Export module .
     *
     * @param $module
     * @return \Alchemy\Zippy\Archive\ArchiveInterface
     * @throws ExportException
     */
    public function exportModule($module) {
        $path = storage_path(
            config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $module
        );

        if( ! Support\is_path_exists(
            $path
        ) )
            throw new ExportException(_("Invalid module path."));

        return Support\download_archive(
            $path, str_replace('/', '_', $module)
        );
    }


    #@todo fix generated code ..
    protected function fixer($path) {
        $finder = \Symfony\CS\Finder\DefaultFinder::create()
            ->in($path)
        ;

        return \Symfony\CS\Config\Config::create()
            ->level(\Symfony\CS\FixerInterface::PSR2_LEVEL)
            ->finder($finder);
    }

    #@todo find a clear way to rollback migrations .
    protected function flushDatabase($connection, $path) {
        $db = DB::connection($connection)->getDatabaseName();
        DB::statement('DROP DATABASE `'.$db.'`');
        DB::statement('CREATE DATABASE `'.$db.'`');

        return Support\artisan('migrate', [
            '--database' => $connection, '--path' => $path
        ]);
    }
}