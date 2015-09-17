<?php

namespace Flysap\ScaffoldGenerator\Packages;

class Imageable extends Package implements PackageInterface {

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
}