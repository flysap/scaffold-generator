<?php

use Illuminate\Http\Request;

Route::group(['prefix' => 'admin/scaffold-generator', 'middleware' => 'role:admin'], function() {

    Route::match(['post', 'get'], 'generate', ['as' => 'scaffold-generate', function(Request $request) {
        $service = app('scaffold-manager');

        if( $request->method() == 'POST' ) {
            $isGenerated = true;

            $pathModule = $service->generate(
                $request->all()
            );

        return back()
            ->withInput([
                'is_generated' => $isGenerated,
                'path_module'  => $pathModule,
                'vendor'      => $request->get('vendor'),
                'name'        => $request->get('name'),
            ] + $request->all());
        }

        $packages = config('scaffold-generator.packages');

        return view('scaffold-generator::generate', [
            'is_generated' => old('is_generated'),
            'path_module'  => old('path_module'),
            'vendor'      => old('vendor'),
            'name'        => old('name'),
            'packages'    => array_except($packages, ['scaffold']),
            'scaffold'    => config('scaffold')
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

    Route::any('flush-modules', ['as' => 'flush-modules', function() {
        $scaffold = app('scaffold-manager');

        $scaffold->flushModules();

        return redirect()
            ->back();
    }]);

    Route::any('flush-module', ['as' => 'flush-module', function(Request $request) {
        $scaffold = app('scaffold-manager');

        $scaffold->flushModule(
            $request->get('module')
        );

        return redirect()
            ->back();
    }]);

    Route::any('export-module', ['as' => 'export-module', function(Request $request) {
        $scaffold = app('scaffold-manager');

        return $scaffold->exportModule(
            $request->get('module')
        );
    }]);
});