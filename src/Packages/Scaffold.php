<?php

namespace Flysap\ScaffoldGenerator\Packages;

class Scaffold extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return "use ScaffoldTrait;\n";
    }

    /**
     * @return mixed
     */
    public function options() {
        return '';
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return 'implements ScaffoldAble';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Flysap\\Scaffold\\Traits\\ScaffoldTrait;\nuse Flysap\\Scaffold\\ScaffoldAble;";
    }
}