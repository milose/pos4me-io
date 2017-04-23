<?php

namespace app\Auth;

use App\Operater;
use App\OperaterIdent;
use App\Exceptions\UnauthorizedException;

class Login
{
    public static function ean()
    {
        $ean = request('ean');

        if ($ean) {
            $ident = OperaterIdent::find($ean);
            
            if ($ident) {
                if ($ident->operater) {
                    //@TODO: FIX THIS ASAP
                    if (version_compare(phpversion(), '7.1', '>')) {
                        return strtoupper(bin2hex($ident->operater->password));
                    } else {
                        return strtoupper(($ident->operater->password));
                    }
                }
            }
        } else {
            throw new UnauthorizedException('Malformed query.', 400);
            return;
        }

        throw new UnauthorizedException('User not found.', 401);
    }

    public static function basic()
    {
        $user = request('user');
        $pass = request('pass');

        if ($user && $pass) {
            $users = Operater::authBasic($user, $pass);

            if ($users->count() > 0) {
                //@TODO: FIX THIS ASAP
                if (version_compare(phpversion(), '7.1', '>')) {
                    return strtoupper(bin2hex($users->first()->password));
                } else {
                    return strtoupper(($users->first()->password));
                }
            }
        } else {
            throw new UnauthorizedException('Malformed query.', 400);
            return;
        }

        throw new UnauthorizedException('User not found.', 401);
    }
}
