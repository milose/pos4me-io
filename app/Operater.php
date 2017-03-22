<?php

namespace app;

use OperaterIdent;
use Illuminate\Database\Eloquent\Model;

class Operater extends Model
{
    protected $table = 'operater';

    protected $primaryKey = 'username';

    public $timestamps = false;
    
    public $incrementing = false;

    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }
    
    public function scopeAuthBasic($query, $username, $password)
    {
        return $query->isActive()
                        ->where('username', $username)
                        ->whereRaw("password = HASHBYTES('MD5', '{$password}')");
    }

    public function scopeWithToken($query, $token)
    {
        return $query->isActive()
                        ->whereRaw("CONVERT(VARCHAR(MAX), password, 2) = '{$token}'");
    }

    public function kljucevi()
    {
        return $this->hasMany('App\OperaterIdent', 'username', 'username');
    }
}
