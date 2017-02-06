<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumentStavka extends Model
{
    protected $table = 'artikal_dnevnik';
    
    protected $primaryKey = 'id_dnevnik';
    
    public function osobine()
    {
        return $this->hasMany('App\DokumentStavkaOsobina', 'id_dnevnik');
    }
    
    public function artikal()
    {
        return $this->belongsTo('App\Artikal', 'id_artikal', 'id_artikal');
    }
}
