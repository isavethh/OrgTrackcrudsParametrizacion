<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class UnidadMedidaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $unidadesMedida = UnidadMedida::orderBy('nombre')->get();

        return response()->json([
            'success' => true,
            'data' => $unidadesMedida
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(UnidadMedida $unidadMedida)
    {
        return response()->json([
            'success' => true,
            'data' => $unidadMedida
        ]);
    }
}

