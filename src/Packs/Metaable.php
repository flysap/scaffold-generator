<?php

namespace Flysap\ScaffoldGenerator\Packs;

class Metaable extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use MetaTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', Metaable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Laravel\\Meta\\Eloquent\\Metaable;use Laravel\\Meta\\Eloquent\\MetaTrait;\n";
    }
}