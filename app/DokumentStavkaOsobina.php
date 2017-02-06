<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DokumentStavkaOsobina extends Model
{
    protected $table = 'artikal_dnevnik_osobina';

    public function stavka()
    {
        return $this->belongsTo('App\DokumentStavka', 'id_dnevnik', 'id_dnevnik');
    }
}