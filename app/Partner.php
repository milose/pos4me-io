<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Partner extends Model
{
    protected $table = 'partner';

    protected $primaryKey = 'id_partner';

    public function osobine()
    {
        return $this->hasMany('App\Dokument', 'id_partner');
    }
}
