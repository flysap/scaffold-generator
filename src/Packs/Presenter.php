<?php

namespace Flysap\ScaffoldGenerator\Packs;

class Presenter extends Package implements PackageInterface {

    /**
     * @return mixed
     */
    public function traits() {
        return "use PresentableTrait;\n";
    }

     /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Laracasts\\Presenter\\PresentableTrait;";
    }

    public function options() {
        return "protected \$presenter = 'UserPresenter';";
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