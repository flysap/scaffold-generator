<?php

namespace Flysap\ScaffoldGenerator\Packages;

use Flysap\ScaffoldGenerator\PackageAble;
use Flysap\Support;

class CommentsAble extends Package implements PackageAble {

    /**
     * @return mixed
     */
    public function traits() {
        return "    use CommentTrait;\n";
    }

    /**
     * @return mixed
     */
    public function contracts() {
        return ', Commentable';
    }

    /**
     * Get import data .
     *
     * @return mixed
     */
    public function import() {
        return "\nuse Eloquent\\Commentable\\CommentTrait;\nEloquent\\Commentable\\Commentable;";
    }

    /**
     * Build some templates for that package .
     *
     * @return $this
     */
    public function buildDependency() {
        Support\artisan('vendor:publish', [
            '--provider' => 'Eloquent\Commentable\CommentsServiceProvider',
            '--tag'      => ['migrations']
        ]);

        return $this;
    }
}