<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class SearchAble extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use SearchableTrait;\n";
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Nicolaslopezj\\Searchable\\SearchableTrait;\n";
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