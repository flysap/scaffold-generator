<?php

namespace Flysap\ScaffoldGenerator\Parsers;

class Field {

    /**
     * @var
     */
    protected $fields;

    /**
     * @var
     */
    private $aliases;

    /**
     * @var string
     */
    protected $rule = "/^(?<field>\w+):(?<type>\w+)(?:\((?<value>.+)\))?(?::(\w+))?\|?(?<unsigned>unsigned)?\|?(?<nullable>nullable)?\|?(?<index>index)?$/i";

    public function __construct($aliases) {

        $this->aliases = $aliases;
    }

    /**
     * Set fields .
     *
     * @param $fields
     * @return $this
     */
    public function setFields($fields) {
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
     * Parse fields .
     *
     * @return array
     */
    public function parse() {
        $fields = explode(",", $this->getFields());
        $fields = preg_replace('/ +/', '', $fields);

        return array_map(function($field)  {
            if( preg_match($this->rule, $field, $matches) )
                return $this->toType($matches);

        }, $fields);
    }

    /**
     * Convert string to array type .
     *
     * @param $matches
     * @return array
     */
    public function toType($matches) {
        $field = ['name' => $matches['field']];

        if( isset($this->aliases[$matches['type']]) )
            $field = array_merge($field, ['type' => $this->aliases[$matches['type']]]);
        else
            $field = array_merge($field, ['type' => $matches['type']]);

        if( isset($matches['value']) )
            $field = array_merge($field, [
               'value' => $matches['value']
            ]);

        if( isset($matches['unsigned']) && $matches['unsigned'] )
            $field = array_merge($field, [
                'unsigned' => $matches['unsigned']
            ]);

        if( isset($matches['nullable']) && $matches['nullable'] )
            $field = array_merge($field, [
                'nullable' => $matches['nullable']
            ]);

        if( isset($matches['index']) && $matches['index'] )
            $field = array_merge($field, [
                'index' => $matches['index']
            ]);

        return $field;
    }
}