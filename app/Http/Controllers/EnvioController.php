<?php

namespace App\Http\Controllers;

use App\Models\Envio;
use App\Models\Admin;
use App\Models\TipoEmpaque;
use App\Models\UnidadMedida;
use Illuminate\Http\Request;

class EnvioController extends Controller
{
    public function index()
    {
        $envios = Envio::with(['admin.usuario', 'tipoEmpaque', 'unidadMedida', 'direcciones'])
            ->orderBy('id', 'desc')
            ->get();
        return view('envios.index', compact('envios'));
    }

    public function create()
    {
        $admins = Admin::with('usuario')->get();
        $tiposEmpaque = TipoEmpaque::all();
        $unidadesMedida = UnidadMedida::all();
        
        return view('envios.create', compact('admins', 'tiposEmpaque', 'unidadesMedida'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:admin,id',
            'tipo_empaque_id' => 'nullable|exists:tipo_empaque,id',
            'unidad_medida_id' => 'nullable|exists:unidad_medida,id',
            'estado' => 'required|in:Pendiente,Asignado,En curso,Entregado,Parcialmente entregado',
            'peso' => 'nullable|numeric|min:0',
        ]);

        Envio::create($validated);

        return redirect()->route('envios.index')
            ->with('success', 'Envío creado exitosamente.');
    }

    public function show(Envio $envio)
    {
        $envio->load(['admin.usuario', 'tipoEmpaque', 'unidadMedida', 'direcciones']);
        return view('envios.show', compact('envio'));
    }

    public function edit(Envio $envio)
    {
        $admins = Admin::with('usuario')->get();
        $tiposEmpaque = TipoEmpaque::all();
        $unidadesMedida = UnidadMedida::all();
        
        return view('envios.edit', compact('envio', 'admins', 'tiposEmpaque', 'unidadesMedida'));
    }

    public function update(Request $request, Envio $envio)
    {
        $validated = $request->validate([
            'admin_id' => 'required|exists:admin,id',
            'tipo_empaque_id' => 'nullable|exists:tipo_empaque,id',
            'unidad_medida_id' => 'nullable|exists:unidad_medida,id',
            'estado' => 'required|in:Pendiente,Asignado,En curso,Entregado,Parcialmente entregado',
            'peso' => 'nullable|numeric|min:0',
        ]);

        $envio->update($validated);

        return redirect()->route('envios.index')
            ->with('success', 'Envío actualizado exitosamente.');
    }

    public function destroy(Envio $envio)
    {
        $envio->delete();

        return redirect()->route('envios.index')
            ->with('success', 'Envío eliminado exitosamente.');
    }
}
