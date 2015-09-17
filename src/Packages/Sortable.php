<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Sortable extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use SortableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', Sortable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Eloquent\\Sortable;use Eloquent\\SortableTrait;\n";
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