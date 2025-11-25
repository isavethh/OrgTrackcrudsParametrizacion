<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\RolUsuario;
use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Usuario::with(['rol', 'cliente'])
            ->whereHas('rol', function($query) {
                $query->where('codigo', 'CLIENT');
            })
            ->get();
        
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'ci' => 'required|string|max:20|unique:usuarios,ci',
                'telefono' => 'required|string|max:20',
                'correo' => 'required|email|max:100|unique:usuarios,correo',
                'contrasena' => 'required|string|min:6|max:100',
            ]);

            // Buscar el rol de cliente
            $rolCliente = RolUsuario::where('codigo', 'CLIENT')->first();

            // Crear usuario con datos de persona
            $usuario = Usuario::create([
                'correo' => $validated['correo'],
                'contrasena' => Hash::make($validated['contrasena']),
                'id_rol' => $rolCliente->id,
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
            ]);

            // Crear cliente
            Cliente::create([
                'id_usuario' => $usuario->id,
            ]);

            DB::commit();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear el cliente: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $cliente = Usuario::with(['cliente'])->findOrFail($id);
        return view('clientes.edit', compact('cliente'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $usuario = Usuario::findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'ci' => 'required|string|max:20|unique:usuarios,ci,' . $usuario->id,
                'telefono' => 'required|string|max:20',
                'correo' => 'required|email|max:100|unique:usuarios,correo,' . $usuario->id,
            ]);

            // Actualizar usuario con datos de persona
            $usuarioData = [
                'correo' => $validated['correo'],
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
            ];

            if ($request->filled('contrasena')) {
                $usuarioData['contrasena'] = Hash::make($request->contrasena);
            }

            $usuario->update($usuarioData);

            DB::commit();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al actualizar el cliente: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            DB::commit();

            return redirect()->route('clientes.index')
                ->with('success', 'Cliente eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el cliente: ' . $e->getMessage()]);
        }
    }
}
