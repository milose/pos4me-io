<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Operater extends Model
{
    protected $table = 'operater';
    
    public function scopeIsActive($query)
    {
        return $query->where('is_active', 1);
    }
    
    public function scopeAuthByEan($query, $ean)
    {
        return $query->isActive()
                        ->where('ean', $ean);
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
                        ->where('token', $token);
    }
}
