<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\Support\Traits\ElementAttributes;
use Illuminate\Contracts\Support\Arrayable;

abstract class Package implements Arrayable {

    use ElementAttributes;

    /**
     * @var \Illuminate\Foundation\Application|mixed
     */
    protected $stubGenerator;

    /**
     * @param array $options
     */
    public function __construct($options = array()) {

        $this->setAttributes($options);

        $this->stubGenerator = app('stub-generator');
    }

    /**
     * @return mixed
     */
    public function traits() {
        return '';
    }

    /**
     * @return mixed
     */
    public function options() {
        return ($attributes = $this->getAttribute('attributes')) ? $attributes : '';
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return '';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return '';
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    abstract public function buildDependency();

    /**
     * To array convert .
     *
     * @return array
     */
    public function toArray() {
        return [
            'traits'    => $this->traits(),
            'options'   => $this->options(),
            'contracts' => $this->contracts(),
            'import'    => $this->import(),
        ];
    }
}