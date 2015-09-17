<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;

class Search extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use SearchableTrait;\n";
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "use Nicolaslopezj\\Searchable\\SearchableTrait;\n";
    }

    public function options() {
        return <<<EOD


    /**
     * Searchable rules.
     *
     * @var array
     */
    protected \$searchable = [
        'columns' => [
            'first_name' => 10,
            'last_name' => 10,
            'bio' => 2,
            'email' => 5,
            'posts.title' => 2,
            'posts.body' => 1,
        ],
        'joins' => [
            'posts' => ['users.id','posts.user_id'],
        ],
    ];

EOD;

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