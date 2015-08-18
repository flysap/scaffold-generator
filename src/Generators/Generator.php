<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Flysap\ScaffoldGenerator\Parsers\Field;
use Flysap\ScaffoldGenerator\Parsers\Relation;
use Flysap\ScaffoldGenerator\StubGenerator;
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
    protected $replacement;

    /**
     * @var
     */
    protected $fields;

    /**
     * @var
     */
    protected $relations;


    public function __construct(Filesystem $filesystem, StubGenerator $stubGenerator, Field $fieldParser, Relation $relationParser) {


        $this->filesystem = $filesystem;
        $this->stubGenerator = $stubGenerator;
        $this->fieldParser = $fieldParser;
        $this->relationParser = $relationParser;
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
     * @param $fields
     * @return $this
     */
    public function setReplacement($fields) {
        $this->replacement = $fields;

        return $this;
    }

    /**
     * Merge replacement .
     *
     * @param $fields
     * @return $this
     */
    public function mergeReplacement($fields) {
        $this->replacement = array_merge(
            $fields, $this->getReplacement()
        );

        return $this;
    }

    /**
     * Get replacement .
     *
     * @return mixed
     */
    public function getReplacement() {
        return $this->replacement;
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
        if( $this->filesystem->exists(
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
     */
    public function save($path) {
        $this->stubGenerator->loadStub(
            $this->getStub()
        );

        return $this->stubGenerator
                ->addFields($this->getReplacement())
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