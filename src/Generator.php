<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Parsers\Field;
use Flysap\ScaffoldGenerator\Parsers\Relation;
use Symfony\Component\Filesystem\Filesystem;

abstract class Generator {

    /**
     * @var Filesystem
     */
    protected $filesystem;

    /**
     * @var StubGenerator
     */
    protected $stubGenerator;

    /**
     * @var Field
     */
    protected $fieldParser;

    /**
     * @var Relation
     */
    protected $relationParser;

    /**
     * @var
     */
    protected $fields;

    /**
     * @var
     */
    protected $relations;

    /**
     * @var
     */
    protected $table;

    public function __construct(Filesystem $filesystem, StubGenerator $stubGenerator, Field $fieldParser, Relation $relationParser) {

        $this->filesystem = $filesystem;
        $this->stubGenerator = $stubGenerator;
        $this->fieldParser = $fieldParser;
        $this->relationParser = $relationParser;
    }

    /**
     * Set table .
     *
     * @param $table
     * @return $this
     */
    public function setTable($table) {
        $this->table = $table;

        return $this;
    }

    /**
     * Get table
     *
     * @return mixed
     */
    public function getTable() {
        return $this->table;
    }

    /**
     * Set raw fields .
     *
     * @param array $fields
     * @return $this
     */
    public function setRawFields($fields = array()) {
        $this->fields = $fields;

        return $this;
    }

    /**
     * Set fields .
     *
     * @param array $fields
     * @return $this
     */
    public function setFields($fields = array()) {
        $this->fields = $this->fieldParser
            ->setFields($fields)
            ->parse();

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
     * Set raw relations .
     *
     * @param array $relations
     * @return $this
     */
    public function setRawRelations($relations = array()) {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Set relations .
     *
     * @param array $relations
     * @return $this
     */
    public function setRelations($relations = array()) {
        $this->relations = $this->relationParser
            ->setRelations($relations)
            ->parse();

        return $this;
    }

    /**
     * Get relations .
     *
     * @return mixed
     */
    public function getRelations() {
        return $this->relations;
    }

    /**
     * Generate migration /
     */
    public function generate() {
        return $this->prepare()
            ->generate();
    }

    /**
     * Save migration .
     *
     * @param $path
     */
    public function save($path) {
        return $this->prepare()
            ->save($path);
    }
}