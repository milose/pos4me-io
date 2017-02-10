<?php

use App\Operater;
use App\Dokument;
use App\Auth\Login;
use App\DokumentVrsta;
use App\DokumentStatus;
use Illuminate\Http\Request;

/*
    Login
 */
Route::post('/login/basic', 'LoginController@basic');
Route::post('/login/ean', 'LoginController@ean');

/*
    API
 */
Route::group(['middleware' => 'token'], function () {

    /*
        Informacije o operateru
     */
    Route::get('operater', 'OperaterController@show');

    /*
        Informacije o dokumentima
     */
    Route::get('dokument/spisak', function () {
        $ulazVrste = DokumentVrsta::byName('RMUL')->get();
        $ulaz = ucitajPoVrsti($ulazVrste);

        $izlazVrste = DokumentVrsta::byName('RMIZ')->get();
        $izlaz = ucitajPoVrsti($izlazVrste);
        
        $vrste = array();
        
        if ($ulazVrste->count() > 0) {
            $vrste[] = 
                [
                    'id' => $ulazVrste->first()->id_vrsta,
                    'skraceni' => $ulazVrste->first()->skraceni,
                    'naziv' => $ulazVrste->first()->naziv,
                    'opis' => $ulazVrste->first()->opis,
                    'count' => $ulaz->count(),
                ];
        }
        
        if ($izlazVrste->count() > 0) {
            $vrste[] = 
                [
                    'id' => $izlazVrste->first()->id_vrsta,
                    'skraceni' => $izlazVrste->first()->skraceni,
                    'naziv' => $izlazVrste->first()->naziv,
                    'opis' => $izlazVrste->first()->opis,
                    'count' => $izlaz->count(),
                ];
        }

        return response()->json(compact('vrste'), 200);
    });

    /*
        Listaj dokumenta po vrsti
     */
    Route::get('dokument/vrsta/{id}', function ($id) {
        $vrsta = DokumentVrsta::find($id);
        
        if (!$vrsta) {
            return response()->json(['dokumenti' => []], 200);
        }
        
        $dokumenti = DokumentVrsta::find($vrsta->id_vrsta)
                        ->dokumenti()
                        ->with([
                            'status' => function ($query) {
                                $query->whereIn('vrsta', [
                                    'PDA-S',
                                    'PDA-D'
                                ]);
                            }
                        ])
                        ->get()
                        ->filter->status
                        ->flatten();
        
        return response()->json(compact('dokumenti'), 200);
    });

    /*
        Nađi dokument po ean
     */
    Route::get('dokument/find/{ean}', function ($ean) {
        $dokument = Dokument::byEan($ean)->first();
        return response()->json(compact('dokument'), 200);
    });

    /*
        Dokument
     */
    Route::get('dokument/{id}', function ($id) {
        $dokument = Dokument::with('status')->find($id);
        return response()->json(compact('dokument'), 200);
    });

    /*
        Dokument veze
     */
    Route::get('dokument/{id}/vezani', function ($id) {
        $dokument = Dokument::with('status')->find($id);
        
        if (!$dokument) {
            return response()->json(['dokument' => []], 200);
        }
        
        $dokument = Dokument::with('status')->find($dokument->veza->vezani->id_dokument);

        return response()->json(compact('dokument'), 200);
    });

    /*
        Listaj stavke za dokument
     */
    // @TODO: Refactor, makni filter u komentarima i if u each
    Route::get('dokument/{id}/stavke', function ($id) {
        $dokument = Dokument::with('status')->find($id);
        
        if (!$dokument) {
            return response()->json(['stavke' => []], 200);
        }
        
        $stavke = $dokument->stavke()
                            ->with('osobine', 'artikal.eans')->get()
                            ->each(function ($stavka) {
                                $stavka->naziv = $stavka->artikal->naziv;
                                $stavka->eans = $stavka->artikal->eans
                                                    // ->filter(function ($ean) {
                                                    //     return !is_string($ean->osobine);
                                                    // })
                                                    ->each(function ($ean) use ($stavka) {
                                                        if (is_array($ean->osobine)) {
                                                            $ean->osobine = json_encode($ean->osobine);
                                                        }
                                                        $ean->osobine = json_decode($ean->osobine, true);
                                                        // dd($ean);
                                                    });
                            });

        return response()->json(compact('stavke'), 200);
    });

    /*
        Promijeni kolicine za stavke
     */
    Route::post('dokument/{dokument}/stavke', function (Dokument $dokument) {
        collect(request()->data)->each(function ($stavka) use ($dokument) {
            $dokument->stavke->find($stavka['id_dnevnik'])->update($stavka);
        });

        return response('', 200);
    });

    /*
        Podesi status
     */
    // @TODO: fix this shit
    Route::get('dokument/{dokument}/status/{set}', function (Dokument $dokument, $set) {
        $status = $dokument->status()->kontrola()->first();
        $status->vrsta = $set;
        App\DokumentStatus::kontrola()->where('id_dokument', 5)->update(['vrsta' => $status->vrsta]);

        return;
    });
});
