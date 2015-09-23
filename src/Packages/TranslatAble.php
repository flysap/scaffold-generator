<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;
use Flysap\Support;

class TranslatAble extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use TranslatableTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', Translatable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Eloquent\\Translatable\\Translatable;\nuse Eloquent\\Translatable\\TranslatableTrait;\n";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        Support\artisan('vendor:publish', [
            '--provider' => 'Localization\LocaleServiceProvider'
        ]);

        Support\artisan('vendor:publish', [
            '--provider' => 'Translator\TranslatorServiceProvider'
        ]);

        return $this;
    }
}