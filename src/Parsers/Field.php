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
    protected $rule = "/^(\\w+):(\\w+)(?:\\((.+)\\))?(?::(\\w+))?$/i";

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
        $field = ['name' => $matches[1]];

        if( isset($this->aliases[$matches[2]]) )
            $field = array_merge($field, ['type' => $this->aliases[$matches[2]]]);
        else
            $field = array_merge($field, ['type' => $matches[2]]);

        if( isset($matches[3]) )
            $field = array_merge($field, [
               'value' => $matches[3]
            ]);

        if( isset($matches[4]) )
            $field = array_merge($field, [
                'unsigned' => $matches[4]
            ]);

        return $field;
    }
}