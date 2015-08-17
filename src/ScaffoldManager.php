<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\ExportException;
use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Flysap\ScaffoldGenerator\Generators\ContextGenerator;
use Flysap\Support;

class ScaffoldManager {

    /**
     * Generate scaffold .
     *
     * @param $params
     * @return string
     */
    public function generate($params) {
        try {
            $path = DIRECTORY_SEPARATOR . $params['vendor'] . DIRECTORY_SEPARATOR . $params['name'];

            $generator = app('generator');
            $tables    = $params['tables'];

            $generator->generate(
                ContextGenerator::GENERATOR_MIGRATION
            )
                ->setContents($tables)
                ->save($path);


            $generator->generate(
                ContextGenerator::GENERATOR_MODEL
            )
                ->setContents($params)
                ->save($path);


            $generator->generate(
                ContextGenerator::GENERATOR_COMPOSER
            )
                ->setReplacement(array_only($params, ['name', 'vendor', 'description', 'version']))
                ->save($path . DIRECTORY_SEPARATOR . 'composer.json');


            $generator->generate(
                ContextGenerator::GENERATOR_CONFIG
            )
                ->setContents($params)
                ->save($path . DIRECTORY_SEPARATOR . 'module.json');


            return 'storage/' . config('scaffold-generator.temp_path') . $path;

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
}