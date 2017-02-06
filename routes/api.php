<?php

use App\Operater;
use App\Dokument;
use App\Auth\Login;
use App\DokumentVrsta;
use App\DokumentStatus;
use Illuminate\Http\Request;
use App\Exceptions\UnauthorizedException;

/*
    Login
 */
Route::post('/login/basic', 'LoginController@basic');
Route::post('/login/ean', 'LoginController@ean');

/*
    API
 */
Route::group(['middleware' => 'token'], function () {
    
    // Informacije o operateru
    Route::get('operater', function () {
        return request()->operater;
    });
    
    // Svi dokumenti
    Route::get('dokument/all', function () {
        $ulazVrste = DokumentVrsta::byName('RMUL')->get();
        $ulaz = loadType($ulazVrste);
        
        $izlazVrste = DokumentVrsta::byName('RMIZ')->get();
        $izlaz = loadType($izlazVrste);
        
        $data = [
            'RMUL' => [
                'naziv' => $ulazVrste->first()->naziv,
                'opis' => $ulazVrste->first()->opis,
                'count' => $ulaz->count(),
                ],
            'RMIZ' => [
                'naziv' => $izlazVrste->first()->naziv,
                'opis' => $izlazVrste->first()->opis,
                'count' => $izlaz->count(),
                ],
            ];
        
        return $data;
    });
    
    // Listaj dokumenta po vrsti
    Route::get('dokument/ulaz/{vrsta}', function ($vrsta) {
        $vrsta = DokumentVrsta::byName($vrsta)->get();
        $ulaz = loadType($vrsta);
        
        return $ulaz;
    });
    
    // Nađi dokument po ean
    Route::get('dokument/find/{ean}', function ($ean) {
        return Dokument::byEan($ean)->first();
    });
        
    
    
    // Dokument
    Route::get('dokument/{id}', function ($id) {
        return Dokument::find($id);
    });
    
    // Dokument veze
    Route::get('dokument/{id}/veza', function ($id) {
        return Dokument::find($id)->veza->vezani;
    });
    
    // Listaj stavke za dokument
    Route::get('dokument/{id}/stavke', function ($id) {
        return Dokument::find($id)->stavke()->with('osobine', 'artikal.eans')->get();
    });
});

Route::get('test', function () {
    return DokumentStatus::byStatus(['PDA-S', 'PDA-D'])->with(['dokument' => function ($query) {
            $query->whereIn('id_vrsta', 13);
        }])
        ->get();
});

function loadType($vrsta) {
    return DokumentStatus::byStatus(['PDA-S', 'PDA-D'])->with(['dokument' => function ($query) use ($vrsta) {
            $query->whereIn('id_vrsta', $vrsta->pluck('id_vrsta'));
        }])
        ->filter(function ($item) {
            // samo gdje ima dokument
            return !is_null($item->dokument);
        });
}