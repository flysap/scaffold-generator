<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\Support\Traits\ElementAttributes;
use Illuminate\Contracts\Support\Arrayable;

abstract class Package implements Arrayable {

    use ElementAttributes;

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
        return '';
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
    public function buildDepency() {
        return $this;
    }

    /**
     * To array convert .
     *
     * @return array
     */
    public function toArray() {
        return [
            'Packages_traits'    => $this->traits(),
            'Packages_options'   => $this->options(),
            'Packages_contracts' => $this->contracts(),
            'Packages_import'    => $this->import(),
        ];
    }
}