<?php

namespace Flysap\ScaffoldGenerator\Parsers;

class Relation {

    /**
     * @var
     */
    protected $relations;

    /**
     * @var string
     */
    protected $rule = "/(?<foreign>\\w+):(?<reference>\\w+)\\|(?<table>\\w+)\\|?(?<on_update>\\w+)?\\|?(?<on_delete>\\w+)?\\|?(?<relation>hasOne|hasMany|belongsTo|belongsToMany)?/i";

    /**
     * Set relations .
     *
     * @param $relations
     * @return $this
     */
    public function setRelations($relations) {
        if(! is_array($relations))
            $relations = (array)$relations;

        $this->relations = $relations;

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
     * Parse Relations .
     *
     * @return array
     */
    public function parse() {
        return array_map(function($field)  {
            if( preg_match($this->rule, $field, $matches) )
                return $this->toType($matches);

        }, $this->getRelations());
    }

    /**
     * Convert string to array type .
     *
     * @param $matches
     * @return array
     */
    public function toType($matches) {
        if(! isset($matches['on_update']))
            $matches['on_update'] = 'no action';

        if(! isset($matches['on_delete']))
            $matches['on_delete'] = 'no action';

        return $matches;
    }
}