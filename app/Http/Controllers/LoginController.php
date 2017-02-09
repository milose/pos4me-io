<?php

namespace app\Http\Controllers;

use App\Auth\Login;

class LoginController extends Controller
{
    public function ean()
    {
        $token = Login::ean();

        return response()->json(['token' => $token], 200);
    }

    public function basic()
    {
        $token = Login::basic();

        return response()->json(['token' => $token], 200);
    }
}
