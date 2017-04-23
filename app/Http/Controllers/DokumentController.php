<?php

namespace App\Http\Controllers;

use DB;
use App\Operater;
use App\Dokument;
use Carbon\Carbon;
use App\Auth\Login;
use App\DokumentVrsta;
use App\DokumentStatus;
use Illuminate\Http\Request;

class DokumentController extends Controller
{

    /*
        Informacije o dokumentima
     */
    public function spisak()
    {
        $ulazVrste = DokumentVrsta::byName(env('DOC_ULAZ'))->get();
        $ulaz = ucitajPoVrsti($ulazVrste);

        $izlazVrste = DokumentVrsta::byName(env('DOC_IZLAZ'))->get();
        $izlaz = ucitajPoVrsti($izlazVrste);
        
        $vrste = array();
        
        if ($ulazVrste->count() > 0) {
            foreach ($ulazVrste as $vrsta) {
                $count = $ulaz->filter(function ($item) use ($vrsta) {
                    return $item->dokument->id_vrsta == $vrsta->id_vrsta;
                })->count();
                
                if ($count <= 0) {
                    continue;
                }
                
                $vrste[] =
                    [
                        'id' => $vrsta->id_vrsta,
                        'skraceni' => $vrsta->skraceni,
                        'naziv' => $vrsta->naziv,
                        'opis' => $vrsta->opis,
                        'count' => $count,
                        'vrsta' => 'U',
                    ];
            }
        }
        
        if ($izlazVrste->count() > 0) {
            foreach ($izlazVrste as $vrsta) {
                $count = $izlaz->filter(function ($item) use ($vrsta) {
                    return $item->dokument->id_vrsta == $vrsta->id_vrsta;
                })->count();
                
                if ($count <= 0) {
                    continue;
                }
                
                $vrste[] =
                    [
                        'id' => $vrsta->id_vrsta,
                        'skraceni' => $vrsta->skraceni,
                        'naziv' => $vrsta->naziv,
                        'opis' => $vrsta->opis,
                        'count' => $count,
                        'vrsta' => 'I',
                    ];
            }
        }
        
        return response()->json(compact('vrste'), 200);
    }
    
    /*
        Listaj dokumenta po vrsti
     */
    public function vrsta($id)
    {
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
                                $query->pda()->whereIn('vrijednost', ['S', 'D']);
                            }
                        ])
                        ->get()
                        ->each(function ($doc) use ($ulaz, $izlaz) {
                            $doc->tip = in_array($doc->vrsta->skraceni, $ulaz) ?
                                                    'U' : (in_array($doc->vrsta->skraceni, $izlaz) ?
                                                            'I' : null);
                        })
                        ->filter->status
                        ->flatten();
        
        return response()->json(compact('dokumenti'), 200);
    }
    
    /*
        Dokument
     */
    public function show($id)
    {
        $dokument = Dokument::with('status', 'partner')->find($id);
        
        if (!$dokument) {
            return response()->json(null, 404);
        }
        
        $ulaz = json_decode(env('DOC_ULAZ'));
        $izlaz = json_decode(env('DOC_IZLAZ'));
        
        $dokument->tip = in_array($dokument->vrsta->skraceni, $ulaz) ?
                                'U' : (in_array($dokument->vrsta->skraceni, $izlaz) ?
                                        'I' : null);
                                        
        return response()->json(compact('dokument'), 200);
    }
    
    /*
        Dokument veze
     */
    public function showVezani($id)
    {
        $dokument = Dokument::with('status')->find($id);
        
        if (!$dokument || !$dokument->veza) {
            return response()->json(null, 404);
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
    }
    
    /*
        Listaj stavke za dokument
     */
    public function showStavke($id)
    {
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
    }
    
    /*
        Promijeni kolicine za stavke
     */
    public function updateStavke(Dokument $dokument)
    {
        collect(request()->data)->each(function ($stavka) use ($dokument) {
            $dokument->stavke->find($stavka['id_dnevnik'])->update($stavka);
        });

        return response('', 200);
    }
    
    /*
        Podesi status
     */
    public function updateStatus($id, $set)
    {
        $vrijednost = '';

        switch ($set) {
            case 'lock':
                $vrijednost = 'P';
                break;
            case 'unlock':
                $vrijednost = 'D';
                break;
            case 'done':
                $vrijednost = 'Z';
                break;
        }
        
        $data = Operater::withToken(request()->headers->get('Api-Token'));
        $operater = $data->first();
        
        $doc = Dokument::find($id)->veza->vezani;
        $doc->updated = Carbon::now();
        $doc->op_update = $operater->username;
        $doc->save();
        
        DokumentStatus::pda()
                        ->where('id_dokument', $id)
                        ->update(['vrijednost' => $vrijednost]);

        return response('', 200);
    }
    
    public function format()
    {
        //select * from parametar where naziv = 'RMDokFormat'
        $format = DB::table('parametar')->select('vrijednost')->where('naziv', 'RMDokFormat')->first();
        return response()->json($format, 200);
    }
}
