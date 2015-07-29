<?php

namespace Flysap\ScaffoldGenerator\Packages;

class Sluggable extends Package implements PackageInterface {

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
        return "use Cviebrock\\EloquentSluggable\\SluggableInterface;\nuse Cviebrock\\EloquentSluggable\\SluggableTrait;";
    }
}