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
    
    /*
        Informacije o operateru
     */
    Route::get('operater', function () {
        $data = Operater::withToken(request()->headers->get('Api-Token'));
        return response()->json($data->first(), 200);
    });
    
    /*
        Informacije o dokumentima
     */
    Route::get('dokument/spisak', function () {
        $ulazVrste = DokumentVrsta::byName('RMUL')->get();
        $ulaz = ucitajPoVrsti($ulazVrste);
        
        $izlazVrste = DokumentVrsta::byName('RMIZ')->get();
        $izlaz = ucitajPoVrsti($izlazVrste);
        
        $vrste = [
            [
                'id' => $ulazVrste->first()->id_vrsta,
                'skraceni' => $ulazVrste->first()->skraceni,
                'naziv' => $ulazVrste->first()->naziv,
                'opis' => $ulazVrste->first()->opis,
                'count' => $ulaz->count(),
            ],
            [
                'id' => $izlazVrste->first()->id_vrsta,
                'skraceni' => $izlazVrste->first()->skraceni,
                'naziv' => $izlazVrste->first()->naziv,
                'opis' => $izlazVrste->first()->opis,
                'count' => $izlaz->count(),
            ],
        ];
        
        return response()->json(compact('vrste'), 200);
    });
    
    /*
        Listaj dokumenta po vrsti
     */
    Route::get('dokument/vrsta/{vrsta}', function (DokumentVrsta $vrsta) {
        $dokumenti = Dokument::with('status')
                        ->where('id_vrsta', $vrsta->id_vrsta)
                        ->get()
                        ->whereIn('status.vrsta', ['PDA-S', 'PDA-D'])
                        ->flatten();
        
        return response()->json(compact('dokumenti'), 200);
    });
    
    /*
        Nađi dokument po ean
     */
    Route::get('dokument/find/{ean}', function ($ean) {
        return Dokument::byEan($ean)->first();
    });
        
    
    
    /*
        Dokument
     */
    Route::get('dokument/{dokument}', function (Dokument $dokument) {
        $dokument = Dokument::with('status')->find($dokument->id_dokument);
        return response()->json($dokument, 200);
    });
    
    /*
        Dokument veze
     */
    Route::get('dokument/{dokument}/vezani', function (Dokument $dokument) {
        $dokument = Dokument::with('status')->find($dokument->veza->vezani->id_dokument);
        return response()->json($dokument, 200);
    });
    
    /*
        Listaj stavke za dokument
     */
    // @TODO: Refactor, makni filter u komentarima i if u each
    Route::get('dokument/{dokument}/stavke', function (Dokument $dokument) {
        $stavke =  $dokument->stavke()
                            ->with('osobine', 'artikal.eans')->get()
                            ->each(function ($stavka) {
                                $stavka->naziv = $stavka->artikal->naziv;
                                $stavka->eans = $stavka->artikal->eans
                                                    // ->filter(function ($ean) {
                                                    //     return !is_string($ean->osobine);
                                                    // })
                                                    ->each(function ($ean) use($stavka) {
                                                        if (is_array($ean->osobine)) $ean->osobine = json_encode($ean->osobine);
                                                        $ean->osobine = json_decode($ean->osobine, true);
                                                        // dd($ean);
                                                    });
                                                ;
                            });
        return response()->json(compact('stavke'), 200);
    });
    
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
    
    /*
        Stavke
     */
});

/*
 * Helpers
 */
function ucitajPoVrsti($vrsta) {
    return DokumentStatus::where('vrsta', 'PDA-S')
                            ->orWhere('vrsta', 'PDA-D')
                            ->with(['dokument' => function ($query) use ($vrsta) {
                                $query->whereIn('id_vrsta', $vrsta->pluck('id_vrsta'));
                            }])
                            ->get()
                            ->filter(function ($item) {
                                // samo gdje ima dokument
                                return !is_null($item->dokument);
                            });
}