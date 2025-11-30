<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\TipoVehiculo;
use Illuminate\Http\Request;

class TipoVehiculoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tiposVehiculo = TipoVehiculo::orderBy('nombre')->get();

        return response()->json([
            'success' => true,
            'data' => $tiposVehiculo
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(TipoVehiculo $tipoVehiculo)
    {
        return response()->json([
            'success' => true,
            'data' => $tipoVehiculo
        ]);
    }
}

