<?php

namespace Flysap\ScaffoldGenerator\Parsers;

class Relations {

    /**
     * @var
     */
    protected $relations;

    /**
     * @var string
     */
    protected $rule = "/^(\\w+):(\\w+)(?:\\((.+)\\))?(?::(\\w+))?$/i";

    public function setRelations($relations) {
        $this->relations = $relations;

        return $this;
    }

    public function getRelations() {
        return $this->relations;
    }

    /**
     * Parse Relations .
     *
     * @return array
     */
    public function parse() {
        $Relations = explode(",", $this->getRelations());

        return array_map(function($field)  {
            if( preg_match($this->rule, $field, $matches) )
                return $this->toType($matches);

        }, $Relations);
    }

    /**
     * Convert string to array type .
     *
     * @param $matches
     * @return array
     */
    public function toType($matches) {

    }
}