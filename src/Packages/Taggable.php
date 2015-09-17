<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Taggable extends Package implements PackageAble {

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

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        return $this;
    }
}