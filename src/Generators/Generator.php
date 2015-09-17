<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Flysap\ScaffoldGenerator\Parsers\Field;
use Flysap\ScaffoldGenerator\Parsers\Relation;
use Flysap\ScaffoldGenerator\StubGenerator;
use Flysap\Support;

abstract class Generator {

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
     * @var array
     */
    protected $contents = [];

    /**
     * @var
     */
    protected $stub;

    /**
     * @var
     */
    protected $replacements = [];

    /**
     * @var
     */
    protected $fields;

    /**
     * @var
     */
    protected $relations;

    protected $packageManager;

    public function __construct(StubGenerator $stubGenerator, Field $fieldParser, Relation $relationParser) {

        $this->stubGenerator = $stubGenerator;
        $this->fieldParser = $fieldParser;
        $this->relationParser = $relationParser;

        $this->init();
    }

    /**
     * Initialize generator .
     *
     * @return mixed
     */
    public function init() {
        $this->packageManager = app('scaffold-package');
    }

    /**
     * Get package manager .
     *
     * @return mixed
     */
    protected function getPackageManager() {
        return $this->packageManager;
    }

    /**
     * Set contents .
     *
     * @param array $contents
     * @return $this
     */
    public function setContents($contents = array()) {
        $this->contents = [];
        $this->addContent($contents);

        return $this;
    }

    /**
     * Add contents .
     *
     * @param array $contents
     * @return $this
     */
    public function addContent($contents = array()) {
        $contents = $this->prepare($contents);

        array_walk($contents, function($content, $key) {
            $this->contents[$key] = $content;
        });

        return $this;
    }

    /**
     * Get all contents .
     *
     * @return array
     */
    public function getContents() {
        return $this->contents;
    }

    /**
     * Get table by alias .
     *
     * @param $alias
     * @return mixed
     */
    public function getContent($alias) {
        if( isset($this->contents[$alias]) )
            return $this->contents[$alias];
    }


    /**
     * Set replacement .
     *
     * @param array $replacements
     * @return $this
     */
    public function setReplacements(array $replacements = array()) {
        $this->replacements = $replacements;

        return $this;
    }

    /**
     * Merge replacement .
     *
     * @param array $replacements
     * @return $this
     */
    public function addReplacement(array $replacements = array()) {
        $this->replacements = array_merge($replacements, $this->replacements);

        return $this;
    }

    /**
     * Get replacement .
     *
     * @return mixed
     */
    public function getReplacements() {
        return $this->replacements;
    }

    /**
     * Clear replacements .
     *
     */
    public function clearReplacements() {
        $this->setReplacements([]);
    }


    /**
     * Set stub path
     *
     * @param $stub
     * @return $this
     */
    public function setStub($stub) {
        $this->stub = $stub;

        return $this;
    }

    /**
     * @return mixed
     */
    abstract function getStub();

    /**
     * Load stub .
     *
     * @return string
     */
    public function loadStub() {
        if( Support\is_path_exists(
            $this->getStub()
        ) )
            return file_get_contents(
                $this->getStub()
            );
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
     * Get fields parser .
     *
     * @return Field
     */
    protected function getFieldsParser() {
        return $this->fieldParser;
    }

    /**
     * Get relations parser .
     *
     * @return Relation
     */
    protected function getRelationsParser() {
        return $this->relationParser;
    }


    /**
     * Prepare content .
     *
     * @param $content
     * @return mixed
     */
    protected function prepare($content) {
        return $content;
    }


    /**
     * Save migration .
     *
     * @param $path
     * @return mixed
     */
    public function save($path) {
        $this->stubGenerator->loadStub(
            $this->getStub()
        );

        return $this->stubGenerator
                ->addFields($this->getReplacements())
                ->save($path);
    }

    /**
     * Generate model
     *
     * @return mixed
     * @throws \Flysap\ScaffoldGenerator\Exceptions\StubException
     */
    public function generate() {
        if(! $this->stubGenerator->isStubLoaded())
            $this->stubGenerator->loadStub(
                $this->getStub()
            );

        return $this->stubGenerator
            ->addFields($this->getReplacement())
            ->generate();
    }
}