<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Exportable extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use ExportableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', Exportable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use DataExporter\\DriverAssets\\Eloquent\\Exportable;\nuse DataExporter\\DriverAssets\\Eloquent\\ExportableTrait;\n";
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