<?php

namespace Flysap\ScaffoldGenerator;

use Flysap\ScaffoldGenerator\Exceptions\StubException;

class ScaffoldManager {

    /**
     * @var StubGenerator
     */
    private $stubGenerator;

    public function __construct(StubGenerator $stubGenerator) {

        $this->stubGenerator = $stubGenerator;
    }

    /**
     * Generate scaffold .
     *
     * @param $post
     */
    public function generate($post) {
        try {
            $fullPath = DIRECTORY_SEPARATOR . $post['vendor'] . DIRECTORY_SEPARATOR . $post['name'];

            /** Generate the module.json file. */
            $this->stubGenerator
                ->loadStub( $this->getStubPath('modules') )
                ->addFields(array_only($post, ['name', 'vendor', 'description', 'version']))
                ->save($fullPath . DIRECTORY_SEPARATOR . 'module.json');

            /** Generate models file . */
            $this->stubGenerator
                ->loadStub( $this->getStubPath('Model') )
                ->addFields(array_only($post, ['name', 'vendor', 'description', 'version']))
                ->save($fullPath . DIRECTORY_SEPARATOR . 'Model.php');


        } catch(StubException $e) {
            dd($e);
        }
    }

    /**
     * Return stub path .
     *
     * @param null $stub
     * @return string
     */
    protected function getStubPath($stub = null) {
        return __DIR__ . '/../stubs/' . (! is_null($stub) ? $stub . '.stub' : '');
    }

}