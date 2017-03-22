<?php

namespace App\Http\Controllers;

use App\Operater;
use Illuminate\Http\Request;

class OperaterController extends Controller
{
    public function show()
    {
        $data = Operater::withToken(request()->headers->get('Api-Token'));
        $operater = $data->first();
        $operater->password = '';
        return response()->json($operater, 200);
    }
}
