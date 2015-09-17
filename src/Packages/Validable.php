<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Validable extends Package implements PackageAble {

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

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        return $this;
    }
}