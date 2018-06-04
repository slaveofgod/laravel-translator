<?php

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => 'admin/translator'
], function () {

    Route::get('/', 'AB\Laravel\Translator\Controllers\DefaultController@index')->name('translator_index');
    Route::get('/language/{language}', 'AB\Laravel\Translator\Controllers\DefaultController@language')->name('translator_language');
    Route::post('/language/{language}/edit', 'AB\Laravel\Translator\Controllers\DefaultController@edit')->name('translator_edit');
});