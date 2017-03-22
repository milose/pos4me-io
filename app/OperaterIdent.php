<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OperaterIdent extends Model
{
    protected $table = 'operater_ident';
    
    protected $primaryKey = 'ident';

    public $timestamps = false;
    
    public $incrementing = false;

    public function operater()
    {
        return $this->belongsTo('App\Operater', 'username', 'username');
    }
}
