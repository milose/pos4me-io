<?php


/*
    Login
 */
Route::post('/login/basic', 'LoginController@basic');
Route::post('/login/ean', 'LoginController@ean');

Route::get('dokument/format', 'DokumentController@format');

/*
    API
 */
Route::group(['middleware' => 'token'], function () {
    Route::get('operater', 'OperaterController@show');
    Route::get('dokument/spisak', 'DokumentController@spisak');
    Route::get('dokument/vrsta/{id}', 'DokumentController@vrsta');
    Route::get('dokument/{id}', 'DokumentController@show');
    Route::get('dokument/{id}/vezani', 'DokumentController@showVezani');
    Route::get('dokument/{id}/stavke', 'DokumentController@showStavke');
    Route::post('dokument/{dokument}/stavke', 'DokumentController@updateStavke');
    Route::post('dokument/{id}/status/{set}', 'DokumentController@updateStatus');
});
