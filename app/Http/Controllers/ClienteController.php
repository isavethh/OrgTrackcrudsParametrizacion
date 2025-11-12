<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cliente;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $clientes = Cliente::with('usuario')->get();
        return view('clientes.index', compact('clientes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $usuarios = Usuario::whereDoesntHave('cliente')
            ->whereDoesntHave('admin')
            ->whereDoesntHave('transportista')
            ->get();
        return view('clientes.create', compact('usuarios'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuario,correo',
            'contrasena' => 'required|min:6',
            'telefono' => 'nullable|string|max:20',
            'direccion_entrega' => 'nullable|string|max:255',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
        ]);

        Cliente::create([
            'usuario_id' => $usuario->id,
            'telefono' => $request->telefono,
            'direccion_entrega' => $request->direccion_entrega,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $cliente = Cliente::with('usuario')->findOrFail($id);
        return view('clientes.show', compact('cliente'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $cliente = Cliente::with('usuario')->findOrFail($id);
        $usuarios = Usuario::where(function($query) use ($cliente) {
                $query->whereDoesntHave('cliente')
                    ->orWhere('id', $cliente->usuario_id);
            })
            ->whereDoesntHave('admin')
            ->whereDoesntHave('transportista')
            ->get();
        return view('clientes.edit', compact('cliente', 'usuarios'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $cliente = Cliente::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuario,correo,' . $cliente->usuario_id,
            'telefono' => 'nullable|string|max:20',
            'direccion_entrega' => 'nullable|string|max:255',
        ]);

        $cliente->usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
        ]);

        if ($request->filled('contrasena')) {
            $cliente->usuario->update([
                'contrasena' => Hash::make($request->contrasena),
            ]);
        }

        $cliente->update([
            'telefono' => $request->telefono,
            'direccion_entrega' => $request->direccion_entrega,
        ]);

        return redirect()->route('clientes.index')->with('success', 'Cliente actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $cliente = Cliente::findOrFail($id);
        $cliente->usuario->delete(); // Esto también eliminará el cliente por CASCADE
        
        return redirect()->route('clientes.index')->with('success', 'Cliente eliminado exitosamente');
    }
}
