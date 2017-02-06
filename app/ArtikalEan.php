<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArtikalEan extends Model
{
    protected $table = 'artikal_eans';
    
    public function artikal()
    {
        return $this->belongsTo('App\Artikal', 'id_artikal', 'id_artikal');
    }
}
