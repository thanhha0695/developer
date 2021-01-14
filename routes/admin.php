<?php

Route::get('example', 'Example\ExampleController@example')->name('example');
Route::post('validate', 'Example\ExampleController@exampleValidate')->name('example.validate');
Route::get('/', function () {
    return view('welcome');
});
Route::group(['middleware' => ['guard' => 'admin']], function () {
    /**@todo */
});
Route::group(['middleware' => ['auth:admin']], function () {
    /**@todo */
});
