<?php

namespace Flysap\ScaffoldGenerator\Packs;

class Imageable extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use ImageableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', ImageableInterface';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Cviebrock\\EloquentImageable\\ImageableInterface;\nuse Cviebrock\\EloquentImageable\\ImageableTrait;\n";
    }
}