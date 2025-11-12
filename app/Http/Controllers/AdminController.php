<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Admin;
use App\Models\Usuario;
use Illuminate\Support\Facades\Hash;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $admins = Admin::with('usuario')->get();
        return view('admins.index', compact('admins'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admins.create');
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
            'nivel_acceso' => 'required|integer|min:1|max:5',
        ]);

        $usuario = Usuario::create([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
            'contrasena' => Hash::make($request->contrasena),
        ]);

        Admin::create([
            'usuario_id' => $usuario->id,
            'nivel_acceso' => $request->nivel_acceso,
        ]);

        return redirect()->route('admins.index')->with('success', 'Administrador creado exitosamente');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $admin = Admin::with('usuario')->findOrFail($id);
        return view('admins.show', compact('admin'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $admin = Admin::with('usuario')->findOrFail($id);
        return view('admins.edit', compact('admin'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $admin = Admin::findOrFail($id);
        
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'correo' => 'required|email|unique:usuario,correo,' . $admin->usuario_id,
            'nivel_acceso' => 'required|integer|min:1|max:5',
        ]);

        $admin->usuario->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido,
            'correo' => $request->correo,
        ]);

        if ($request->filled('contrasena')) {
            $admin->usuario->update([
                'contrasena' => Hash::make($request->contrasena),
            ]);
        }

        $admin->update([
            'nivel_acceso' => $request->nivel_acceso,
        ]);

        return redirect()->route('admins.index')->with('success', 'Administrador actualizado exitosamente');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $admin = Admin::findOrFail($id);
        $admin->usuario->delete(); // Esto también eliminará el admin por CASCADE
        
        return redirect()->route('admins.index')->with('success', 'Administrador eliminado exitosamente');
    }
}
