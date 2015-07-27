<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;
use Flysap\ScaffoldGenerator\Parsers\Fields;
use Flysap\ScaffoldGenerator\Parsers\Relations;
use Symfony\Component\Filesystem\Filesystem;

class MigrationGenerator {

    const STUB_PATH = 'stubs/migration.stub';

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

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var StubGenerator
     */
    private $stubGenerator;

    protected $typeAlias = [
        'varchar' => 'string',
        'int'     => 'integer',
    ];

    /**
     * @var string
     */
    protected $fieldTemplate = '$table->{type}("{name}", "{value}")';

    /**
     * @var Fields
     */
    private $fieldParser;

    /**
     * @var Relations
     */
    private $relationParser;

    public function __construct(Filesystem $filesystem, StubGenerator $stubGenerator, Fields $fieldParser, Relations $relationParser) {

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
     * Prepare to migration .
     *
     * @throws StubException
     */
    public function prepare() {
        if( ! $this->getFields() )
            throw new StubException(_("Invalid fields"));

        if( ! $this->getTable() )
            throw new StubException(_("Invalid table"));

        $tableFields = [];
        $fields      = $this->getFields();
        array_walk($fields, function($field) use(& $tableFields) {
            $string = $this->fieldTemplate;
            foreach ($field as $key => $value) {
                if( $key == 'type' ) {
                    if( isset($this->typeAlias[$value]) )
                        $value = $this->typeAlias[$value];
                }

                $string = str_replace('{'.$key.'}', $value, $string);
            }

            $tableFields[] = $string . ';';
        });

        $this->stubGenerator
            ->loadStub(
                __DIR__ . DIRECTORY_SEPARATOR . '../' . self::STUB_PATH
            );

        $this->stubGenerator
            ->addFields([
                'table_name'   => $this->getTable(),
                'table_fields' => $tableFields,
            ]);

        return $this->stubGenerator;
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