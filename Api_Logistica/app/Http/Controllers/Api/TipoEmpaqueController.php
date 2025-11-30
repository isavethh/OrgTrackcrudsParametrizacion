<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoEmpaque;
use Illuminate\Http\Request;

class TipoEmpaqueController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposEmpaque = TipoEmpaque::orderBy('nombre')->get();

        return response()->json([
            'success' => true,
            'data' => $tiposEmpaque
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoEmpaque $tipoEmpaque)
    {
        return response()->json([
            'success' => true,
            'data' => $tipoEmpaque
        ]);
    }
}

