<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'scaffold-generator'], function() {

    Route::match(['post', 'get'], 'generate', ['as' => 'scaffold-generate', function(Request $request) {
        $service = app('scaffold-manager');

        $isGenerated = false;
        $pathModule  = null;
        $vendor  = null;
        $name  = null;
        if( $request->method() == 'POST' ) {
            $isGenerated = true;

            $pathModule = $service->generate(
                $request->all()
            );

            $vendor = $request->get('vendor');
            $name   = $request->get('name');
        }

        $packages = config('scaffold-generator.package_alias');

        return view('scaffold-generator::generate', [
            'isGenerated' => $isGenerated,
            'pathModule'  => $pathModule,
            'vendor'      => $vendor,
            'name'        => $name,
            'packages'    => array_except($packages, ['scaffold'])
        ]);
    }]);

    Route::post('update-file', ['as' => 'update-file', function(Request $request) {
        Flysap\FileManager\updateFile(
            $request['file'],
            $request['content']
        );
    }]);

    Route::get('load-file', ['as' => 'load-file', function(Request $request) {
        return Flysap\FileManager\getFile(
            $request['file']
        );
    }]);

    Route::post('flush-modules', ['as' => 'flush-modules', function() {
        $scaffold = app('scaffold-manager');

        $scaffold->flushModules();

        return redirect()
            ->back();
    }]);

    Route::post('flush-module', ['as' => 'flush-module', function(Request $request) {
        $scaffold = app('scaffold-manager');

        $scaffold->flushModule(
            $request->get('module')
        );

        return redirect()
            ->back();
    }]);

    Route::post('export-module', ['as' => 'export-module', function(Request $request) {
        $scaffold = app('scaffold-manager');

        return $scaffold->exportModule(
            $request->get('module')
        );
    }]);
});