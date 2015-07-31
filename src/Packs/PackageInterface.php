<?php

namespace Flysap\ScaffoldGenerator\Packs;

interface PackageInterface {

    /**
     * @return mixed
     */
    public function traits();

    /**
     * @return mixed
     */
    public function options();

    /**
     * @return mixed
     */
    public function contracts();

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import();
}