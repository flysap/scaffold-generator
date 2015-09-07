<?php

namespace Flysap\ScaffoldGenerator\Packs;

class Sortable extends Package implements PackageInterface {

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
}