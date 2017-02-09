<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class ArtikalOsobina extends Model
{
    protected $table = 'artikal_osobina';

    public $incrementing = false;
    public $timestamps = false;

    public function artikal()
    {
        return $this->belongsTo('App\Artikal', 'id_artikal', 'id_artikal');
    }
}
