<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class JwtAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->header('Authorization', '');
        if (!str_starts_with($authHeader, 'Bearer ')) {
            return response()->json(['error' => 'No autorizado'], Response::HTTP_UNAUTHORIZED);
        }
        $token = substr($authHeader, 7);

        $secret = env('SECRET_KEY') ?: env('JWT_SECRET');
        if (!$secret) {
            $secret = (string) config('app.key');
            if (str_starts_with($secret, 'base64:')) {
                $secret = base64_decode(substr($secret, 7));
            }
        }

        try {
            $payload = JWT::decode($token, new Key($secret, 'HS256'));
        } catch (\Throwable $e) {
            return response()->json(['error' => 'Token inválido'], Response::HTTP_UNAUTHORIZED);
        }

        $usuario_id = (int) ($payload->sub ?? 0);
        $usuario_rol = (string) ($payload->rol ?? '');
        
        // Obtener datos completos del usuario
        $usuario = \App\Models\Usuario::find($usuario_id);
        if (!$usuario) {
            return response()->json(['error' => 'Usuario no encontrado'], Response::HTTP_UNAUTHORIZED);
        }

        $request->attributes->set('usuario', [
            'id' => $usuario->id,
            'nombre' => $usuario->nombre,
            'apellido' => $usuario->apellido,
            'correo' => $usuario->correo,
            'rol' => $usuario->rol
        ]);

        return $next($request);
    }
}


