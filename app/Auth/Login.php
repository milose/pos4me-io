<?php

namespace App\Auth;

use App\Operater;
use App\Exceptions\UnauthorizedException;

class Login
{
    public static function ean()
    {
        $ean = request('ean');
        
        if ($ean) {
            $users = Operater::authByEan($ean);
            
            if ($users->count() > 0)
            {
                return $users->first()->token;
            }
        }
        
        throw new UnauthorizedException('User not found.');
    }
    
    public static function basic()
    {
        $user = request('user');
        $pass = request('pass');
        
        if ($user && $pass) {
            $users = Operater::authBasic($user, $pass);
            
            if ($users->count() > 0)
            {
                return $users->first()->token;
            }
        }
        
        throw new UnauthorizedException('User not found.');
    }
}