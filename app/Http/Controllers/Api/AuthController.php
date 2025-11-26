<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\Transportista;
use App\Http\Controllers\Api\Helpers\UsuarioHelper;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validar campos requeridos
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'ci' => 'required|string|max:20|unique:persona,ci',
            'correo' => 'required|email|max:100|unique:usuarios,correo',
            'contrasena' => 'required|string|min:6',
            'telefono' => 'nullable|string|max:20',
            'rol' => 'nullable|string|in:cliente,transportista', // Permitir elegir rol
        ]);

        $nombre = $request->input('nombre');
        $apellido = $request->input('apellido');
        $ci = $request->input('ci');
        $correo = $request->input('correo');
        $contrasena = $request->input('contrasena');
        $telefono = $request->input('telefono');
        $rol = $request->input('rol', 'cliente'); // Por defecto cliente

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['error' => 'El correo no es válido'], Response::HTTP_BAD_REQUEST);
        }

        return DB::transaction(function () use ($nombre, $apellido, $ci, $correo, $contrasena, $telefono, $rol) {
            // Verificar que el CI no exista en persona
            $ciExiste = Persona::where('ci', $ci)->exists();
            if ($ciExiste) {
                return response()->json(['error' => 'El CI ya está registrado'], Response::HTTP_CONFLICT);
            }

            // Verificar que el correo no exista
            $correoExiste = Usuario::where('correo', $correo)->exists();
            if ($correoExiste) {
                return response()->json(['error' => 'El correo ya está registrado'], Response::HTTP_CONFLICT);
            }

            // Crear persona primero
            $persona = Persona::create([
                'nombre' => $nombre,
                'apellido' => $apellido,
                'ci' => $ci,
                'telefono' => $telefono,
            ]);

            // Obtener rol (cliente o transportista)
            $idRol = UsuarioHelper::obtenerRolPorCodigo($rol);

            // Crear usuario
            $usuario = Usuario::create([
                'correo' => $correo,
                'contrasena' => Hash::make($contrasena),
                'id_rol' => $idRol,
                'id_persona' => $persona->id,
            ]);

            // Crear registro específico según el rol
            if ($rol === 'transportista') {
                $idEstadoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('Disponible');
                Transportista::create([
                    'id_usuario' => $usuario->id,
                    'id_estado_transportista' => $idEstadoDisponible,
                ]);
            } elseif ($rol === 'cliente') {
                // Crear registro en la tabla cliente
                Cliente::create([
                    'id_usuario' => $usuario->id,
                ]);
            }

            $mensaje = $rol === 'transportista' 
                ? 'Transportista registrado correctamente' 
                : 'Cliente registrado correctamente';

            return response()->json([
                'mensaje' => $mensaje,
                'usuario' => [
                    'id' => $usuario->id,
                    'correo' => $usuario->correo,
                    'nombre' => $persona->nombre,
                    'apellido' => $persona->apellido,
                    'ci' => $persona->ci,
                    'telefono' => $persona->telefono,
                    'rol' => $rol,
                ]
            ], Response::HTTP_CREATED);
        });
    }

    public function login(Request $request)
    {
        $correo = (string) $request->input('correo');
        $contrasena = (string) $request->input('contrasena');

        if ($correo === '' || $contrasena === '') {
            return response()->json(['error' => 'Todos los campos son obligatorios'], Response::HTTP_BAD_REQUEST);
        }

        $usuario = Usuario::with(['rol', 'persona'])->where('correo', $correo)->first();
        if (!$usuario) {
            return response()->json(['error' => 'Credenciales inválidas'], Response::HTTP_UNAUTHORIZED);
        }
        if (!Hash::check($contrasena, $usuario->contrasena)) {
            return response()->json(['error' => 'Credenciales inválidas'], Response::HTTP_UNAUTHORIZED);
        }

        $rolCodigo = $usuario->rol?->codigo ?? 'cliente';

        $payload = [
            'sub' => $usuario->id,
            'rol' => $rolCodigo,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 4),
        ];

        $secret = env('SECRET_KEY') ?: env('JWT_SECRET');
        if (!$secret) {
            $secret = (string) config('app.key');
            if (str_starts_with($secret, 'base64:')) {
                $secret = base64_decode(substr($secret, 7));
            }
        }

        $token = JWT::encode($payload, $secret, 'HS256');

        return response()->json([
            'token' => $token,
            'usuario' => [
                'id' => $usuario->id,
                'nombre' => $usuario->persona?->nombre,
                'apellido' => $usuario->persona?->apellido,
                'rol' => $rolCodigo,
            ],
        ]);
    }
}


