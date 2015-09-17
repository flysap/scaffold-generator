<?php

namespace Flysap\ScaffoldGenerator\Packages;

class Presenter extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use PresentableTrait;\n";
    }

     /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Laracasts\\Presenter\\PresentableTrait;\n";
    }

    public function options() {
        $class = $this->getAttribute('class');

        return "protected \$presenter = '{$class}Presenter';\n";
    }

    /**
     * Build package presenters .
     *
     * @return $this
     */
    public function buildDepency() {
        $this->stubGenerator
            ->loadStub(__DIR__ . '/../../stubs/packages/presenter.stub');

        $this->stubGenerator
            ->addFields([
               'class' => $this->getAttribute('class')
            ])->save(
                $this->getAttribute('path') . DIRECTORY_SEPARATOR . $this->getAttribute('class') . 'Presenter.php'
            );

        return $this;
    }
}