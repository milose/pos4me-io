<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumentStatus extends Model
{
    protected $table = 'artikal_dokument_status';
    
    protected $primaryKey = 'id_dokument';
    
    public $incrementing = false;
    public $timestamps = false;
    
    public function setVrstaAttribute($set)
    {
        $vrsta = '';
        
        switch($set) {
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
        
        $this->attributes['vrsta'] = $vrsta;
    }
    
    public function scopeByStatus($query, array $statusi)
    {
        $query->whereIn('vrsta', $statusi);
    }
    
    public function scopeKontrola($query)
    {
        $query->where('vrijednost', 'kontrola');
    }
        
    public function dokument()
    {
        return $this->belongsTo('App\Dokument', 'id_dokument', 'id_dokument');
    }
}
