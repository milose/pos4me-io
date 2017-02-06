<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumentStatus extends Model
{
    protected $table = 'artikal_dokument_status';
    
    public function scopeByStatus($query, array $statusi)
    {
        $query->whereIn('vrijednost', $statusi);
    }
        
    public function dokument()
    {
        return $this->belongsTo('App\Dokument', 'id_dokument', 'id_dokument');
    }
}
