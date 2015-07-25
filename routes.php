<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'scaffold-generator'], function() {

    Route::match(['post', 'get'], 'generate', ['as' => 'scaffold-generate', function(Request $request) {
        $service = app('scaffold-generator');

        if( $request->method() == 'POST' )
            $service->generate(
                $request->all()
            );

        return view('scaffold-generator::generate');
    }]);
});