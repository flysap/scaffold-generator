<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Imageable extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use ImageAbleTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', ImageAble';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Eloquent\\ImageAble\\ImageAble;\nuse Eloquent\\ImageAble\\ImageAbleTrait;\n";
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