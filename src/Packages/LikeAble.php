<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;
use Flysap\Support;

class LikeAble extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use LikeableTrait;\n";
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "\nuse Parfumix\\Likeable\\LikeableTrait;\n";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        Support\artisan('vendor:publish', [
            '--provider' => 'Parfumix\Likeable\LikeableServiceProvider',
            '--tag'      => ['migrations']
        ]);

        return $this;
    }
}