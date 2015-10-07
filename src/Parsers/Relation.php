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
    protected $regex = "/(?<foreign>\\w+):(?<reference>\\w+)\\|(?<table>\\w+)\\|?(?<on_update>\\w+)?\\|?(?<on_delete>\\w+)?\\|?(?<relation>hasOne|hasMany|belongsToMany)?/i";

    /**
     * Set raw relations ..
     *
     * @param $relations
     * @return $this
     */
    public function setRelations($relations) {
        $this->relations = $relations;

        return $this;
    }

    /**
     * Set relations .
     *
     * @param $relations
     * @return $this
     */
    public function setRawRelations($relations) {
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
        $relations = $this->getRelations();

        if(! is_array($this->getRelations())) {
            $relations = explode(",", $relations);
            $relations = preg_replace('/ +/', '', $relations);
        }

        return array_map(function($relation)  {
            if( preg_match($this->regex, $relation, $matches) )
                return $this->toType($matches);

        }, $relations);
    }

    /**
     * Convert string to array type .
     *
     * @param $matches
     * @return array
     */
    public function toType($matches) {
        if(! isset($matches['on_update']))
            $matches['on_update'] = 'cascade';

        if(! isset($matches['on_delete']))
            $matches['on_delete'] = 'cascade';

        return $matches;
    }
}