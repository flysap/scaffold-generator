<?php

namespace Flysap\ScaffoldGenerator\Packages;

class Taggable extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use TaggableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', TaggableInterface';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Cartalyst\\Tags\\TaggableTrait;\nuse Cartalyst\\Tags\\TaggableInterface;\n";
    }
}