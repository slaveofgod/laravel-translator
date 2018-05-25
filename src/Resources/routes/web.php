<?php

Route::group([
    'middleware' => ['web', 'auth'],
    'prefix' => 'admin/translator'
], function () {

    Route::get('/', 'Translator\Controllers\DefaultController@index')->name('translator_index');
});