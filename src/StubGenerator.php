<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Flysap\Support;

class StubGenerator {

    /**
     * @var
     */
    protected $stub;

    /**
     * @var
     */
    protected $fields;

    /**
     * Load stub .
     *
     * @param $path
     * @return $this
     * @throws StubException
     */
    public function loadStub($path) {
        if( ! Support\is_path_exists(
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
     * Set stub .
     *
     * @param $stub
     * @return $this
     */
    public function setStub($stub) {
        $this->stub = $stub;

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
            throw new StubException(_('Replacement are not loaded'));

        $stub   = $this->getStub();
        $fields = $this->getFields();

        array_walk($fields, function($field, $key) use(& $stub) {
            if( is_array($field) )
                $field = implode("\n", $field);
            elseif($field instanceof \Closure)
                $field = $field();

            $stub = str_replace('{{'.$key.'}}', $field, $stub);
        });

        return $stub;
    }

    /**
     * Save stub .
     *
     * @param $path
     * @return mixed
     */
    public function save($path) {
        $storePath = config('scaffold-generator.temp_path');

        return Support\dump_file(
            storage_path($storePath . $path), $this->generate()
        );
    }


    /**
     * Check if stub is loaded .
     *
     * @return mixed
     */
    public function isStubLoaded() {
        return $this->stub;
    }

    /**
     * Get stub .
     *
     * @return mixed
     */
    public function getStub() {
        return $this->stub;
    }

    /**
     * Return stub path .
     *
     * @param null $stub
     * @return string
     */
    public static function getStubPath($stub = null) {
        return __DIR__ . '/../stubs/' . (! is_null($stub) ? $stub . '.stub' : '');
    }
}