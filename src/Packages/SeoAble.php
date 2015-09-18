<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;
use Flysap\Support;

class SeoAble extends Package implements PackageAble {

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

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        Support\artisan('vendor:publish', [
            '--provider' => 'Laravel\Meta\MetaSeoServiceProvider',
            '--tag'      => ['migrations']
        ]);


        return $this;
    }
}