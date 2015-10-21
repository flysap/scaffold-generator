<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;
use Flysap\Support;

class ShopAble extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use ShopTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', ShopAble';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Laravel\\Shop\\ShopAble;\nuse Laravel\\Shop\\Traits\\ShopTrait;\n";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        Support\artisan('vendor:publish', [
            '--provider' => 'Laravel\Shop\ShopServiceProvider',
            '--tag'      => ['migrations']
        ]);

        return $this;
    }
}