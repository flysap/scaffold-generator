<?php

namespace Flysap\ScaffoldGenerator\Packs;

use Flysap\Support\Traits\ElementAttributes;
use Illuminate\Contracts\Support\Arrayable;

abstract class Package implements Arrayable {

    use ElementAttributes;

    /**
     * @param array $options
     */
    public function __construct($options = array()) {

        $this->setAttribute('options', $options);
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
    public function build() {
        return $this;
    }

    /**
     * To array convert .
     *
     * @return array
     */
    public function toArray() {
        return [
            'packs_traits'    => $this->traits(),
            'packs_options'   => $this->options(),
            'packs_contracts' => $this->contracts(),
            'packs_import'    => $this->import(),
        ];
    }
}