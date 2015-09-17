<?php

namespace Flysap\ScaffoldGenerator;

class PackageManager {

    /**
     * @var array
     */
    protected $packages = array();

    /**
     * @var
     */
    protected $defaultPackages;

    public function __construct(array $packages = array()) {
        $this->setPackages($packages);
    }

    /**
     * Set packages .
     *
     * @param array $packages
     * @return $this
     */
    public function setPackages(array $packages = array()) {
        $this->packages = $packages;

        return $this;
    }

    /**
     * Get all packages .
     *
     * @return array
     */
    public function getPackages() {
        return $this->packages;
    }

    /**
     * Get package .
     *
     * @param $package
     * @return mixed
     */
    public function getPackage($package) {
        if( $this->hasPackage($package) )
            return $this->packages[$package];
    }

    /**
     * Check if has Package .
     *
     * @param $package
     * @return bool
     */
    public function hasPackage($package) {
        return isset($this->packages[$package]);
    }

    /**
     * Set default packages .
     *
     * @param array $packages
     * @return $this
     */
    public function setDefaultPackages(array $packages = array()) {
        $this->defaultPackages = $packages;

        return $this;
    }

    /**
     * Get default Packages .
     *
     * @return mixed
     */
    public function getDefaultPackages() {
        return $this->defaultPackages;
    }

    /**
     * Get package instance .
     *
     * @param $package
     * @param array $attributes
     * @return mixed
     */
    public function packageInstance($package, array $attributes = array()) {
        if( $this->hasPackage($package) )  {
            $options = $this->getPackage($package);

            if( isset($options['class']) && class_exists($options['class']) )
                return (new $options['class']($attributes));
        }
    }

}