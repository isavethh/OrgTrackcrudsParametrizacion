<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\Persona;
use App\Models\RolUsuario;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Usuario::with(['persona', 'rol', 'admin'])
            ->whereHas('rol', function($query) {
                $query->where('codigo', 'ADMIN');
            })
            ->get();
        
        return view('admins.index', compact('admins'));
    }

    public function create()
    {
        return view('admins.create');
    }

    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $validated = $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'ci' => 'required|string|max:20|unique:persona,ci',
                'telefono' => 'required|string|max:20',
                'correo' => 'required|email|max:100|unique:usuarios,correo',
                'contrasena' => 'required|string|min:6|max:100',
                'nivel_acceso' => 'required|integer|min:1|max:5',
            ]);

            // Buscar el rol de admin
            $rolAdmin = RolUsuario::where('codigo', 'ADMIN')->first();

            // Crear persona
            $persona = Persona::create([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
            ]);

            // Crear usuario
            $usuario = Usuario::create([
                'correo' => $validated['correo'],
                'contrasena' => Hash::make($validated['contrasena']),
                'id_rol' => $rolAdmin->id,
                'id_persona' => $persona->id,
            ]);

            // Crear admin
            Admin::create([
                'id_usuario' => $usuario->id,
                'nivel_acceso' => $validated['nivel_acceso'],
            ]);

            DB::commit();

            return redirect()->route('admins.index')
                ->with('success', 'Administrador creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear el administrador: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $admin = Usuario::with(['persona', 'admin'])->findOrFail($id);
        return view('admins.edit', compact('admin'));
    }

    public function update(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $usuario = Usuario::with('persona')->findOrFail($id);

            $validated = $request->validate([
                'nombre' => 'required|string|max:100',
                'apellido' => 'required|string|max:100',
                'ci' => 'required|string|max:20|unique:persona,ci,' . $usuario->id_persona,
                'telefono' => 'required|string|max:20',
                'correo' => 'required|email|max:100|unique:usuarios,correo,' . $usuario->id,
                'nivel_acceso' => 'required|integer|min:1|max:5',
            ]);

            // Actualizar persona
            $usuario->persona->update([
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
            ]);

            // Actualizar usuario
            $usuarioData = [
                'correo' => $validated['correo'],
            ];

            if ($request->filled('contrasena')) {
                $usuarioData['contrasena'] = Hash::make($request->contrasena);
            }

            $usuario->update($usuarioData);

            // Actualizar admin
            $usuario->admin->update(['nivel_acceso' => $validated['nivel_acceso']]);

            DB::commit();

            return redirect()->route('admins.index')
                ->with('success', 'Administrador actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al actualizar el administrador: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $usuario = Usuario::with('persona')->findOrFail($id);
            $persona = $usuario->persona;
            $usuario->delete();
            $persona->delete();

            DB::commit();

            return redirect()->route('admins.index')
                ->with('success', 'Administrador eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el administrador: ' . $e->getMessage()]);
        }
    }
}
