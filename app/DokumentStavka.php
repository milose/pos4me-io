<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class DokumentStavka extends Model
{
    protected $table = 'artikal_dnevnik';

    protected $primaryKey = 'id_dnevnik';

    protected $fillable = ['kol'];

    //protected $touches = ['dokument'];

    public $timestamps = false;

    public function osobine()
    {
        return $this->hasMany('App\DokumentStavkaOsobina', 'id_dnevnik');
    }

    public function artikal()
    {
        return $this->belongsTo('App\Artikal', 'id_artikal', 'id_artikal');
    }

    public function dokument()
    {
        return $this->belongsTo('App\Dokument', 'id_dokument', 'id_dokument');
    }
}
