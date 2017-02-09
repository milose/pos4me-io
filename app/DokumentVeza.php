<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class DokumentVeza extends Model
{
    protected $table = 'artikal_dokument_veza';

    public $incrementing = false;
    public $timestamps = false;

    public function original()
    {
        return $this->belongsTo('App\Dokument', 'id_dokument', 'id_dokument');
    }

    public function vezani()
    {
        return $this->hasOne('App\Dokument', 'id_dokument', 'id_connected');
    }
}
