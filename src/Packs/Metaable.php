<?php

namespace Flysap\ScaffoldGenerator\Packs;

class MetaAble extends Package implements PackageInterface {

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
        return "use Eloquent\\Meta\\Metaable;use Eloquent\\Meta\\MetaTrait;\n";
    }
}