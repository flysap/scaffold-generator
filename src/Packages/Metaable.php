<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;
use Flysap\Support;

class MetaAble extends Package implements PackageAble {

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
        return "use Eloquent\\Meta\\Metaable;\nuse Eloquent\\Meta\\MetaTrait;\n";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        Support\artisan('vendor:publish', [
            '--provider' => 'Eloquent\Meta\MetaServiceProvider',
            '--tag'      => ['migrations']
        ]);

        return $this;
    }
}