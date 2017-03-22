<?php


/*
    Login
 */
Route::post('/login/basic', 'LoginController@basic');
Route::post('/login/ean', 'LoginController@ean');

Route::get('dokument/format', 'DokumentController@format');

Route::get('test', function() {
    return DB::table('korisnik')->value('naziv');
});

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
    Route::get('dokument/{id}/status/{set}', 'DokumentController@updateStatus');
    Route::post('dokument/{dokument}/stavke', 'DokumentController@updateStavke');
});
