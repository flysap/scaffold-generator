<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class ApiAble extends Package implements PackageAble {

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        return $this;
    }
}