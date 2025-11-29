<?php

namespace App\Http\Controllers;

use App\Models\Transportista;
use App\Models\Usuario;
use App\Models\RolUsuario;
use App\Models\EstadoTransportista;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class TransportistaController extends Controller
{
    public function index()
    {
        $transportistas = Usuario::with(['rol', 'transportista.estadoTransportista'])
            ->whereHas('rol', function($query) {
                $query->where('codigo', 'TRANSP');
            })
            ->get();
        
        return view('transportistas.index', compact('transportistas'));
    }

    public function create()
    {
        $estadosTransportista = EstadoTransportista::all();
        return view('transportistas.create', compact('estadosTransportista'));
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
                'id_estado_transportista' => 'required|exists:estados_transportista,id',
            ]);

            // Buscar el rol de transportista
            $rolTransportista = RolUsuario::where('codigo', 'TRANSP')->first();

            // Crear usuario con datos de persona
            $usuario = Usuario::create([
                'correo' => $validated['correo'],
                'contrasena' => Hash::make($validated['contrasena']),
                'id_rol' => $rolTransportista->id,
                'nombre' => $validated['nombre'],
                'apellido' => $validated['apellido'],
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
            ]);

            // Crear transportista
            Transportista::create([
                'id_usuario' => $usuario->id,
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
                'id_estado_transportista' => $validated['id_estado_transportista'],
            ]);

            DB::commit();

            return redirect()->route('transportistas.index')
                ->with('success', 'Transportista creado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al crear el transportista: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        $transportista = Usuario::with(['transportista'])->findOrFail($id);
        $estadosTransportista = EstadoTransportista::all();
        return view('transportistas.edit', compact('transportista', 'estadosTransportista'));
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
                'id_estado_transportista' => 'required|exists:estados_transportista,id',
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

            // Actualizar transportista
            $usuario->transportista->update([
                'ci' => $validated['ci'],
                'telefono' => $validated['telefono'],
                'id_estado_transportista' => $validated['id_estado_transportista'],
            ]);

            DB::commit();

            return redirect()->route('transportistas.index')
                ->with('success', 'Transportista actualizado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors(['error' => 'Error al actualizar el transportista: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $usuario = Usuario::findOrFail($id);
            $usuario->delete();

            DB::commit();

            return redirect()->route('transportistas.index')
                ->with('success', 'Transportista eliminado exitosamente.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => 'Error al eliminar el transportista: ' . $e->getMessage()]);
        }
    }
}
