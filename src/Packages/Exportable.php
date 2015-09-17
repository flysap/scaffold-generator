<?php

namespace Flysap\ScaffoldGenerator\Packages;

class Exportable extends Package implements PackageInterface {

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
}