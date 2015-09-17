<?php

namespace Flysap\ScaffoldGenerator\Packages;

class SeoAble extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use MetaSeoTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', MetaSeoable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Laravel\\Meta\\Eloquent\\MetaSeoable;\nuse Laravel\\Meta\\Eloquent\\MetaSeoTrait;\n";
    }
}