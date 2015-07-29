<?php

namespace Flysap\ScaffoldGenerator\Packages;

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
     * To array convert .
     *
     * @return array
     */
    public function toArray() {
        return [
            'packages_traits'    => $this->traits(),
            'packages_options'   => $this->options(),
            'packages_contracts' => $this->contracts(),
            'packages_import'    => $this->import(),
        ];
    }
}