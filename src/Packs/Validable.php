<?php

namespace Flysap\ScaffoldGenerator\Packs;

class Validable extends Package implements PackageInterface {

    public function traits() {
        return "    use ValidatingTrait;\n";
    }

    public function options() {
         return <<<DOC

     /**
     * Validate fields .
     *
     * @var array
     */
    protected \$rules = [
        'title' => 'required|unique:posts|max:255',
        'body' => 'required',
    ];

DOC;

    }

    public function import() {
        return "use Watson\\Validating\\ValidatingTrait;\n";
    }
}