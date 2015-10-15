<?php

use Illuminate\Http\Request;
use Flysap\Support;

Route::group(['prefix' => 'admin/scaffold-generator', 'middleware' => 'role:admin'], function() {

    Route::match(['post', 'get'], 'generate', ['as' => 'scaffold-generate', function(Request $request) {
        $service = app('scaffold-manager');

        if( $request->method() == 'POST' ) {
            $pathModule = $service->generate($request->all());

            return back()
                ->withInput([
                    'is_generated' => true,
                    'path_module'  => $pathModule,
                ] + $request->all());
        }

        $packages  = config('scaffold-generator.packages');
        $templates = config('scaffold-generator.templates');

        return view('scaffold-generator::generate', [
            'packages'  => array_except($packages, ['scaffold']),
            'templates' => $templates,
            'scaffold'  => config('scaffold')
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

    /**
     * Upload an template .
     */
    Route::any('upload-template/{template?}', ['as' => 'upload-template', function(Request $request, $template = null) {
        if( $template ) {
            $templates = config('scaffold-generator.templates');

            if(! array_key_exists($template, $templates))
                return back();

            $template = $templates[$template];

            /** @var If template no exists in storage path than load from local templates . $path */
            $path = file_exists(storage_path($template['path'])) ? storage_path($template['path']) : __DIR__ . DIRECTORY_SEPARATOR . $template['path'];

            if( $contents = Support\get_file_contents( $path ) )
                return redirect(route('scaffold-generate'))
                    ->withInput($contents);
        }

        if(! $request->hasFile('template'))
            return back();

        $file = $request->file('template');

        if( $moved = $file->move(
            storage_path(), $file->getClientOriginalName()
        ) ) {
            if( $contents = Support\get_file_contents( $moved->getRealPath() ) )
                return redirect(route('scaffold-generate'))
                    ->withInput($contents);
        }
    }]);
});