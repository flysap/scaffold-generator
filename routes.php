<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'scaffold-generator'], function() {

    Route::match(['post', 'get'], 'generate', ['as' => 'scaffold-generate', function(Request $request) {
        $service = app('scaffold-generator');

        $isGenerated = false;
        $pathModule  = null;
        if( $request->method() == 'POST' ) {
            $isGenerated = true;

            $pathModule = $service->generate(
                $request->all()
            );
        }

        return view('scaffold-generator::generate', compact('isGenerated', 'pathModule'));
    }]);

    /**
     * Update the file .
     *
     */
    Route::post('update-file', ['as' => 'update-file', function(Request $request) {
        Flysap\FileManager\updateFile(
            $request['file'],
            $request['content']
        );
    }]);

    /**
     * Load the file .
     *
     */
    Route::get('load-file', ['as' => 'load-file', function(Request $request) {
        return Flysap\FileManager\getFile(
            $request['file']
        );
    }]);
});