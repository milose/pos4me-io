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

Route::get('dokument/format', function() {
    //select * from parametar where naziv = 'RMDokFormat'
    $format = DB::table('parametar')->select('vrijednost')->where('naziv', 'RMDokFormat')->first();
    return response()->json($format, 200);
});

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
        $ulazVrste = DokumentVrsta::byName(env('DOC_ULAZ'))->get();
        $ulaz = ucitajPoVrsti($ulazVrste);

        $izlazVrste = DokumentVrsta::byName(env('DOC_IZLAZ'))->get();
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
                    'vrsta' => 'U',
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
                    'vrsta' => 'I',
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
        
        $ulaz = json_decode(env('DOC_ULAZ'));
        $izlaz = json_decode(env('DOC_IZLAZ'));
        
        $dokumenti = DokumentVrsta::find($vrsta->id_vrsta)
                        ->dokumenti()
                        ->with('partner')
                        ->with([
                            'status' => function ($query) {
                                $query->whereIn('vrsta', [
                                    'PDA-S',
                                    'PDA-D'
                                ]);
                            }
                        ])
                        ->get()
                        ->each(function ($doc) use($ulaz, $izlaz) {
                            $doc->tip = in_array($doc->vrsta->skraceni, $ulaz) ?
                                                    'U' : (in_array($doc->vrsta->skraceni, $izlaz) ?
                                                            'I' : null);
                        })
                        ->filter->status
                        ->flatten();
        
        return response()->json(compact('dokumenti'), 200);
    });

    /*
        Dokument
     */
    Route::get('dokument/{id}', function ($id) {
        $dokument = Dokument::with('status', 'partner')->find($id);
        
        if (!$dokument) {
            return response()->json(['dokument' => []], 200);
        }
        
        $ulaz = json_decode(env('DOC_ULAZ'));
        $izlaz = json_decode(env('DOC_IZLAZ'));
        
        $dokument->tip = in_array($dokument->vrsta->skraceni, $ulaz) ?
                                'U' : (in_array($dokument->vrsta->skraceni, $izlaz) ?
                                        'I' : null);
                                        
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
        
        $dokument = Dokument::with('status', 'partner')->find($dokument->veza->vezani->id_dokument);
        
        if (!$dokument) {
            return response()->json(['dokument' => []], 200);
        }
        
        $ulaz = json_decode(env('DOC_ULAZ'));
        $izlaz = json_decode(env('DOC_IZLAZ'));
        
        $dokument->tip = in_array($dokument->vrsta->skraceni, $ulaz) ?
                                'U' : (in_array($dokument->vrsta->skraceni, $izlaz) ?
                                        'I' : null);

        return response()->json(compact('dokument'), 200);
    });

    /*
        Listaj stavke za dokument
     */
    Route::get('dokument/{id}/stavke', function ($id) {
        $dokument = Dokument::find($id);
        
        if (!$dokument) {
            return response()->json(['stavke' => []], 200);
        }
        
        $stavke = $dokument->stavke()
                            ->with('osobine', 'artikal.eans')->get()
                            ->each(function ($stavka) {
                                $stavka->naziv = $stavka->artikal->naziv;
                                $stavka->eans = $stavka->artikal->eans
                                                        ->each(function ($ean) use ($stavka) {
                                                            if (is_array($ean->osobine)) {
                                                                $ean->osobine = json_encode($ean->osobine);
                                                            }
                                                            $ean->osobine = json_decode($ean->osobine, true);
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
    Route::post('dokument/{id}/status/{set}', function ($id, $set) {
        $vrsta = '';

        switch ($set) {
            case 'lock':
                $vrsta = 'PDA-P';
                break;
            case 'unlock':
                $vrsta = 'PDA-D';
                break;
            case 'done':
                $vrsta = 'PDA-Z';
                break;
        }
        
        DokumentStatus::kontrola()
                        ->where('id_dokument', $id)
                        ->update(['vrsta' => $vrsta]);

        return response('', 200);
    });
});
