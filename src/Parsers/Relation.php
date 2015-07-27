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
    protected $rule = "/(?<foreign>\\w+):(?<reference>\\w+)\\|(?<table>\\w+)\\|?(?<on_update>\\w+)?\\|?(?<on_delete>\\w+)?/i";

    /**
     * Set relations .
     *
     * @param $relations
     * @return $this
     */
    public function setRelations($relations) {
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
        $field = [
            'foreign'   => $matches['foreign'],
            'reference' => $matches['reference'],
            'on'        => $matches['table']
        ];

        if( isset($matches['on_update']) )
            $field = array_merge($field, [
                'on_update' => $matches['on_update']
            ]);
        else
            $field = array_merge($field, [
                'on_update' => 'no action'
            ]);


        if( isset($matches['on_delete']) )
            $field = array_merge($field, [
                'on_delete' => $matches['on_delete']
            ]);
        else
            $field = array_merge($field, [
                'on_update' => 'no action'
            ]);

        return $field;
    }
}