<?php

namespace App\Http\Controllers;

use App\Models\Transportista;
use App\Models\Usuario;
use App\Models\EstadoTransportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TransportistaController extends Controller
{
    public function index()
    {
        $transportistas = Transportista::with(['usuario', 'estado'])->get();
        return view('transportistas.index', compact('transportistas'));
    }

    public function create()
    {
        $estados = EstadoTransportista::all();
        return view('transportistas.create', compact('estados'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuario,correo',
            'contrasena' => 'required|min:6',
            'ci' => 'required|string|max:20|unique:transportista,ci',
            'telefono' => 'nullable|string|max:20',
            'estado_id' => 'required|exists:estado_transportista,id',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
        ]);

        Transportista::create([
            'usuario_id' => $usuario->id,
            'ci' => $request->ci,
            'telefono' => $request->telefono,
            'estado_id' => $request->estado_id,
        ]);

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista creado exitosamente.');
    }

    public function edit(Transportista $transportista)
    {
        $estados = EstadoTransportista::all();
        $transportista->load('usuario');
        return view('transportistas.edit', compact('transportista', 'estados'));
    }

    public function update(Request $request, Transportista $transportista)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuario,correo,' . $transportista->usuario_id,
            'ci' => 'required|string|max:20|unique:transportista,ci,' . $transportista->id,
            'telefono' => 'nullable|string|max:20',
            'estado_id' => 'required|exists:estado_transportista,id',
        ]);

        $transportista->usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
        ]);

        if ($request->filled('contrasena')) {
            $transportista->usuario->update([
                'contrasena' => Hash::make($request->contrasena),
            ]);
        }

        $transportista->update([
            'ci' => $request->ci,
            'telefono' => $request->telefono,
            'estado_id' => $request->estado_id,
        ]);

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista actualizado exitosamente.');
    }

    public function destroy(Transportista $transportista)
    {
        $transportista->usuario->delete(); // Esto también eliminará el transportista por CASCADE

        return redirect()->route('transportistas.index')
            ->with('success', 'Transportista eliminado exitosamente.');
    }
}
