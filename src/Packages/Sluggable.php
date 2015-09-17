<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Sluggable extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use SluggableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', SluggableInterface';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Cviebrock\\EloquentSluggable\\SluggableInterface;\nuse Cviebrock\\EloquentSluggable\\SluggableTrait;\n";
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