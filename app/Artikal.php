<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Artikal extends Model
{
    protected $table = 'artikal';
    
    protected $primaryKey = 'id_artikal';
    
    public function osobine()
    {
        return $this->hasMany('App\ArtikalOsobina', 'id_artikal');
    }
    
    public function eans()
    {
        return $this->hasMany('App\ArtikalEan', 'id_artikal');
    }
}
