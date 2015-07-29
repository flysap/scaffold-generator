<?php

namespace Flysap\ScaffoldGenerator\Packages;

class Sluggable extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return 'use SluggableTrait;';
    }

    /**
     * @return mixed
     */
    public function options() {
        return 'return ["from"]';
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
        return "use Cviebrock\\EloquentSluggable\\SluggableInterface;\n use Cviebrock\\EloquentSluggable\\SluggableTrait;";
    }
}