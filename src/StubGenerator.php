<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Symfony\Component\Filesystem\Filesystem;

class StubGenerator {

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var
     */
    protected $stub;

    /**
     * @var
     */
    protected $fields;

    public function __construct(Filesystem $filesystem) {

        $this->filesystem = $filesystem;
    }

    /**
     * Load stub .
     *
     * @param $path
     * @return $this
     * @throws StubException
     */
    public function loadStub($path) {
        if( ! $this->filesystem->exists(
            $path
        ) )
            throw new StubException(
                _("Invalid stub path")
            );

        $this->stub = file_get_contents(
            $path
        );

        return $this;
    }

    /**
     * Add replacements fields .
     *
     * @param array $fields
     * @return $this
     */
    public function addFields($fields = array()) {
        if(! is_array($fields))
            $fields = (array)$fields;

        $this->fields = $fields;

        return $this;
    }

    /**
     * Get fields .
     *
     * @return mixed
     */
    public function getFields() {
        return $this->fields;
    }

    /**
     * Generate stub .
     *
     * @throws StubException
     */
    public function generate() {

        if( ! $this->isStubLoaded() )
            throw new StubException(_('Stub not loaded'));

        if(! $this->getFields())
            throw new StubException(_('Replaement are not loaded'));

        $stub   = $this->getStub();
        $fields = $this->getFields();

        array_walk($fields, function($field, $key) use(& $stub) {
            $stub = str_replace('{{'.$key.'}}', $field, $stub);
        });

        return $stub;
    }

    /**
     * Save stub .
     *
     * @param $path
     * @throws StubException
     */
    public function save($path) {
        $storePath = config('scaffold-generator.temp_path');

        return $this->filesystem
            ->dumpFile(storage_path($storePath . $path), $this->generate());
    }


    /**
     * Check if stub is loaded .
     *
     * @return mixed
     */
    protected function isStubLoaded() {
        return $this->stub;
    }

    /**
     * Get stub .
     *
     * @return mixed
     */
    protected function getStub() {
        return $this->stub;
    }
}