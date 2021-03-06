<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class DokumentStatus extends Model
{
    protected $table = 'artikal_dokument_status';

    protected $primaryKey = 'id_dokument';
    
    protected $fillable = ['vrsta'];

    public $incrementing = false;
    
    public $timestamps = false;

    public function scopePda($query)
    {
        $query->where('vrsta', 'PDA');
    }

    public function dokument()
    {
        return $this->belongsTo('App\Dokument', 'id_dokument', 'id_dokument');
    }
}
