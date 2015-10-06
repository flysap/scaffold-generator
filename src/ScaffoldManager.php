<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Flysap\ScaffoldGenerator\Generators\ComposerGenerator;
use Flysap\ScaffoldGenerator\Generators\ConfigGenerator;
use Flysap\ScaffoldGenerator\Generators\MigrationGenerator;
use Flysap\ScaffoldGenerator\Generators\ModelGenerator;
use Flysap\ScaffoldGenerator\Generators\ServiceProviderGenerator;
use Flysap\Support;
use DB;

class ScaffoldManager {

    public function generate($params) {
        try {
            $params['vendor'] = $this->sluggify($params['vendor']);
            $params['name'] = $this->sluggify($params['name']);

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

            /**
             * Save service provider class .
             */
            $class = ucfirst(str_singular($params['name']));
            $serviceProvider = ServiceProviderGenerator::getInstance();
            $serviceProvider
                ->addReplacement([
                    'class' => ucfirst($class),
                    'vendor' => $params['vendor'],
                    'name' => $params['name'],
                    'boot_action' => '',
                    'register_action' => '',
                ])
                ->save(
                $path . DIRECTORY_SEPARATOR . ucfirst(str_singular($params['name'])) . 'ServiceProvider.php'
            );

            /**
             * Save routes .
             */
            /*$routesGenerator = RoutesGenerator::getInstance();
            $routesGenerator->save(
                $path . DIRECTORY_SEPARATOR . 'routes.php'
            );*/


            $this->fixer(storage_path(
                config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $path
            ));

            $path = 'storage/' . config('scaffold-generator.temp_path') . $path;

            $this->flushDatabase(config('scaffold-generator.development_database'), $path . DIRECTORY_SEPARATOR . 'migrations');

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
     * @throws StubException
     */
    public function exportModule($module) {
        $path = storage_path(
            config('scaffold-generator.temp_path') . DIRECTORY_SEPARATOR . $module
        );

        if( ! Support\is_path_exists(
            $path
        ) )
            throw new StubException(_("Invalid module path."));

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
            ->finder($finder);
    }

    #@todo find a clear way to rollback migrations .
    protected function flushDatabase($connection, $path) {
        $db = DB::connection($connection)->getDatabaseName();
        DB::statement('DROP DATABASE `'.$db.'`');
        DB::statement('CREATE DATABASE `'.$db.'`');

        Support\artisan('migrate', [
            '--database' => $connection,
        ]);

        Support\artisan('migrate', [
            '--database' => $connection, '--path' => $path
        ]);

        Support\artisan('db:seed');

        return true;
    }

    /**
     * Sluggify string .
     *
     * @param $string
     * @return mixed
     */
    protected function sluggify($string) {
        return str_replace(' ', '', $string);
    }
}