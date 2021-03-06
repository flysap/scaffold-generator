<?php

namespace Flysap\ScaffoldGenerator\Generators;

use Flysap\ScaffoldGenerator\Generator;

class ConfigGenerator extends Generator {

    const PREFIX_NAMESPACE = 'Modules';

    public function init() {
        parent::init();

        $this->setStub(
            __DIR__ . DIRECTORY_SEPARATOR .'../../stubs/modules.stub'
        );
    }

    /**
     * Save module.json
     *
     * @param $path
     * @return mixed
     */
    public function save($path) {
        $contents = $this->getContents();

        $menus = [];
        array_walk($contents['tables'], function ($table) use (& $menus) {

            #@todo temporary way .
            if( isset($table['relations']) && !empty($table['relations']) )
                if( strpos($table['relations'], 'belongsToMany') )
                    return false;

            $table = str_singular(strtolower($table['name']));

            $menus[] = [
                'section'   => $this->getVendor(),
                'label'     => str_plural(ucfirst($table)),
                'href'      => route('scaffold::main', [
                    'model' => sprintf('%s/%s/%s', $this->getVendor(), $this->getUser(), $table)
                ]),
            ];
        });

        $this->addReplacement([
            'vendor'      => $this->getVendor(),
            'name'        => $this->getUser(),
            'description' => $this->getDescription(),
            'version'     => $this->getVersion(),
            'autoload'    => function() {
                return',"autoload": "' . sprintf('%s\\\\%s\\\\%s\\\\%s', self::PREFIX_NAMESPACE, ($this->getVendor()), ($this->getUser()), ucfirst(str_singular($this->getUser())).'ServiceProvider') .'"';
            },
            'menus'       => function () use($menus) {
                $html = ',"menu": [';

                $count = 0;

                foreach ($menus as $menu) {
                    $count++;

                    $html .= <<<JSON
        \n      {
          "section": "{$menu['section']}",
          "label"  : "{$menu['label']}",
          "href"   : "{$menu['href']}"
      }\n
JSON;
                    if( $count < count($menus) )
                        $html .= ',';
                }

                $html .= '  ]';

                return $html;
            }
        ]);

        return parent::save($path);
    }

    /**
     * Get version .
     *
     * @return string
     */
    protected function getVersion() {
        if (isset($this->getContents()['version']))
            return $this->getContents()['version'];

        return '';
    }

    /**
     * Get description ..
     *
     * @return string
     */
    protected function getDescription() {
        if (isset($this->getContents()['description']))
            return $this->getContents()['description'];

        return '';
    }

    /**
     * Get user .
     *
     * @return mixed
     */
    protected function getUser() {
        return $this->getContents()['name'];
    }

    /**
     * Get vendor .
     *
     * @return mixed
     */
    protected function getVendor() {
        return $this->getContents()['vendor'];
    }
}