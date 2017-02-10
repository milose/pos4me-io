<?php

namespace App\Http\Controllers;

use App\Operater;
use Illuminate\Http\Request;

class OperaterController extends Controller
{
    public function show()
    {
        $data = Operater::withToken(request()->headers->get('Api-Token'));
        return response()->json($data->first(), 200);
    }
}
