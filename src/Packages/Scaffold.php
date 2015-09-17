<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Scaffold extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "use ScaffoldTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return "implements ScaffoldAble";
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Flysap\\Scaffold\\Traits\\ScaffoldTrait;\nuse Flysap\\Scaffold\\ScaffoldAble;\n";
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