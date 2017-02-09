<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumentVrsta extends Model
{
    protected $table = 'dokument_vrsta';
    
    protected $primaryKey = 'id_vrsta';
    
    public $timestamps = false;
    
    public function dokumenti()
    {
        return $this->hasMany('App\Dokument', 'id_vrsta');
    }
    
    public function scopeByName($query, $skraceni)
    {
        $query->where('skraceni', $skraceni);
    }
}
