<?php

Route::group(['prefix' => 'scaffold-generator'], function() {

    Route::get('generate', function() {
       return view('scaffold-generator::generate');
   });
});