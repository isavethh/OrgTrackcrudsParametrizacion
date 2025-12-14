<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use App\Models\Persona;
use App\Models\Cliente;
use App\Models\Transportista;
use App\Http\Controllers\Api\Helpers\UsuarioHelper;
use App\Http\Controllers\Api\Helpers\EstadoHelper;
use App\Http\Controllers\Api\Helpers\SpatieHelper;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AuthWebController extends Controller
{
    public function showLogin()
    {
        // NO redirigir automáticamente desde aquí para evitar bucles
        // Solo mostrar el formulario de login
        // El middleware se encargará de redirigir si es necesario
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'correo' => 'required|email',
            'contrasena' => 'required',
        ]);

        $usuario = Usuario::with(['rol', 'persona'])
            ->where('correo', $request->correo)
            ->first();

        if (!$usuario || !Hash::check($request->contrasena, $usuario->contrasena)) {
            return back()->withErrors([
                'correo' => 'Las credenciales no coinciden con nuestros registros.',
            ])->withInput();
        }

        $rolCodigo = $usuario->rol?->codigo ?? 'cliente';
        
        // Guardar datos en sesión PRIMERO (antes de Spatie)
        session([
            'usuario_id' => $usuario->id,
            'usuario_correo' => $usuario->correo,
            'usuario_rol' => $rolCodigo,
            'usuario_nombre' => $usuario->persona?->nombre,
            'usuario_apellido' => $usuario->persona?->apellido,
        ]);
        
        // Guardar sesión inmediatamente
        session()->save();
        
        \Illuminate\Support\Facades\Log::info('Login Session Debug:', [
            'session_id' => session()->getId(),
            'session_data' => session()->all(),
        ]);
        
        // Sincronizar rol en Spatie usando el helper (con fallback automático)
        SpatieHelper::asignarRol($usuario, $rolCodigo);

        // Generar token JWT para las llamadas API
        $token = $this->generateJwtToken($usuario, $rolCodigo);

        // Redirigir según el rol y pasar el token
        return $this->redirectByRole()->with('jwt_token', $token);
    }

    public function logout()
    {
        Session::flush();
        return redirect()->route('login')->with('status', 'Sesión cerrada correctamente');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'ci' => 'required|string|max:20|unique:persona,ci',
            'correo' => 'required|email|max:100|unique:usuarios,correo',
            'contrasena' => 'required|string|min:6|confirmed',
            'telefono' => 'nullable|string|max:20',
            'rol' => 'nullable|string|in:cliente,transportista',
        ], [
            'contrasena.confirmed' => 'Las contraseñas no coinciden',
            'ci.unique' => 'El CI ya está registrado',
            'correo.unique' => 'El correo ya está registrado',
        ]);

        return DB::transaction(function () use ($request) {
            $rol = $request->input('rol', 'cliente');

            // Crear persona
            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'ci' => $request->ci,
                'telefono' => $request->telefono,
            ]);

            // Obtener rol
            $idRol = UsuarioHelper::obtenerRolPorCodigo($rol);

            // Crear usuario
            $usuario = Usuario::create([
                'correo' => $request->correo,
                'contrasena' => Hash::make($request->contrasena),
                'id_rol' => $idRol,
                'id_persona' => $persona->id,
            ]);

            // Asignar rol en Spatie para acceso web (usando helper con fallback)
            SpatieHelper::asignarRol($usuario, $rol);

            // Crear registro específico según el rol
            if ($rol === 'transportista') {
                $idEstadoDisponible = EstadoHelper::obtenerEstadoTransportistaPorNombre('Disponible');
                Transportista::create([
                    'id_usuario' => $usuario->id,
                    'id_estado_transportista' => $idEstadoDisponible,
                ]);
            } else {
                Cliente::create([
                    'id_usuario' => $usuario->id,
                ]);
            }

            // Iniciar sesión automáticamente
            session([
                'usuario_id' => $usuario->id,
                'usuario_correo' => $usuario->correo,
                'usuario_rol' => $rol,
                'usuario_nombre' => $persona->nombre,
                'usuario_apellido' => $persona->apellido,
            ]);

            // Generar token JWT para las llamadas API
            $token = $this->generateJwtToken($usuario, $rol);

            return $this->redirectByRole()->with('jwt_token', $token);
        });
    }

    private function redirectByRole()
    {
        $rol = session('usuario_rol', 'cliente');

        return match ($rol) {
            'admin' => redirect()->route('admin.dashboard'),
            'transportista' => redirect()->route('admin.dashboard'),
            default => redirect()->route('dashboard'), // cliente
        };
    }

    /**
     * Genera un token JWT para el usuario
     */
    private function generateJwtToken(Usuario $usuario, string $rolCodigo): string
    {
        $payload = [
            'sub' => $usuario->id,
            'rol' => $rolCodigo,
            'iat' => time(),
            'exp' => time() + (60 * 60 * 4), // 4 horas
        ];

        $secret = env('SECRET_KEY') ?: env('JWT_SECRET');
        if (!$secret) {
            $secret = (string) config('app.key');
            if (str_starts_with($secret, 'base64:')) {
                $secret = base64_decode(substr($secret, 7));
            }
        }

        return JWT::encode($payload, $secret, 'HS256');
    }
}
