<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Presenter extends Package implements PackageAble {

     /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Robbo\\Presenter\\PresentableInterface;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', PresentableInterface';
    }

    /**
     * Return options .
     *
     * @return string
     */
    public function options() {
        $class = ucfirst(str_singular($this->getAttribute('name')));

        return "public function getPresenter() {        return new {$class}Presenter(\$this);    }";
    }

    /**
     * Build package presenters .
     *
     * @return $this
     */
    public function buildDependency() {
        $this->stubGenerator
            ->loadStub(__DIR__ . '/../../stubs/packages/presenter.stub');

        $class = ucfirst(str_singular($this->getAttribute('name')));

        $this->stubGenerator
            ->addFields([
               'class' => $class,
               'vendor' => $this->getAttribute('module')['vendor'],
               'name' => $this->getAttribute('module')['name'],
            ])->save(
                $this->getAttribute('path') . DIRECTORY_SEPARATOR . $class . 'Presenter.php'
            );

        return $this;
    }

}