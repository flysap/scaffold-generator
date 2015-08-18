<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\ExportException;
use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Flysap\Support;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ScaffoldManager {

    public function generate($params) {
        try {
            $module = $params['vendor'] . DIRECTORY_SEPARATOR . $params['name'];

            $this->flushModule(
                $module
            );

            $path = DIRECTORY_SEPARATOR . $module;

            $generator = app('generator');
            $tables    = $params['tables'];

            $generator->generate(
                Generator::GENERATOR_MIGRATION
            )
                ->setContents($tables)
                ->save(DIRECTORY_SEPARATOR . $module);


            $generator->generate(
                Generator::GENERATOR_MODEL
            )
                ->setContents($params)
                ->save($path);


            $generator->generate(
                Generator::GENERATOR_COMPOSER
            )
                ->setReplacement(array_only($params, ['name', 'vendor', 'description', 'version']))
                ->save($path . DIRECTORY_SEPARATOR . 'composer.json');


            $generator->generate(
                Generator::GENERATOR_CONFIG
            )
                ->setContents($params)
                ->save($path . DIRECTORY_SEPARATOR . 'module.json');


            $path = 'storage/' . config('scaffold-generator.temp_path') . $path;

            $this->flushDatabase('sqlite', $path . DIRECTORY_SEPARATOR . 'migrations');

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


    protected function flushDatabase($connection, $path) {
        $tables = DB::connection($connection)
            ->table('sqlite_master')
            ->get();

        array_walk($tables, function($table) {
            if( $table->name ==  'sqlite_sequence' ) return false;

            Schema::connection('sqlite')
                ->dropIfExists($table->name);
        });

        return Support\artisan('migrate', [
            '--database' => $connection, '--path' => $path
        ]);
    }
}