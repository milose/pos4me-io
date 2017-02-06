<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArtikalOsobina extends Model
{
    protected $table = 'artikal_osobina';
    
    public function artikal()
    {
        return $this->belongsTo('App\Artikal', 'id_artikal', 'id_artikal');
    }
}
