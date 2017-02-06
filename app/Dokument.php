<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Dokument extends Model
{
    protected $table = 'artikal_dokument';
    
    protected $primaryKey = 'id_dokument';
    
    public function getSAttribute()
    {
        return $this->status->vrijednost;
    }
    
    public function scopeVrste($query, $vrsta)
    {
        return $query->where('vrsta_id', $vrsta);
    }
    
    public function scopeByEan($query, $ean)
    {
        return $query->where('id_dokument', $ean);
    }
    
    public function status()
    {
        return $this->hasOne('App\DokumentStatus', 'id_dokument');
    }
    
    public function stavke()
    {
        return $this->hasMany('App\DokumentStavka', 'id_dokument');
    }
    
    public function vrsta()
    {
        return $this->belongsTo('App\DokumentVrsta', 'id_dokument');
    }
    
    public function veza()
    {
        return $this->hasOne('App\DokumentVeza', 'id_dokument');
    }
}
