<?php

namespace Flysap\ScaffoldGenerator\Packs;

class Search extends Package implements PackageInterface {

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
        return "use Nicolaslopezj\\Searchable\\SearchableTrait;";
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
}