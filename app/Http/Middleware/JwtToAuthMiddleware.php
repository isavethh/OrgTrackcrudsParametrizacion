<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class JwtToAuthMiddleware
{
    /**
     * Handle an incoming request.
     * Use this middleware to bridge pure JWT auth to Laravel's Session Auth
     * specifically for the Helpdesk Widget integration.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Intentar obtener el token de varias fuentes
        $token = $this->getTokenFromRequest($request);

        // FALLBACK: Si no hay token, verificar si hay una sesión manual activa (Login interno)
        // Asegurar que la sesión esté iniciada
        if (!$request->hasSession()) {
             $request->setLaravelSession(session());
        }
        
        \Illuminate\Support\Facades\Log::info('JwtToAuth Debug:', [
            'has_token' => (bool)$token,
            'session_data' => session()->all(),
            'has_usuario_id' => session()->has('usuario_id'),
        ]);

        if (!$token && session()->has('usuario_id')) {
            Auth::guard('web')->loginUsingId(session('usuario_id'));
            return $next($request);
        }

        if (!$token) {
            // Si no hay token ni sesión, dejamos pasar. El middleware 'auth' posterior redirigirá al login.
            return $next($request);
        }

        // 2. Obtener clave secreta
        $secret = env('SECRET_KEY') ?: env('JWT_SECRET');
        if (!$secret) {
            $secret = (string) config('app.key');
            if (str_starts_with($secret, 'base64:')) {
                $secret = base64_decode(substr($secret, 7));
            }
        }

        try {
            // 3. Decodificar Token
            $payload = JWT::decode($token, new Key($secret, 'HS256'));
            $usuarioId = $payload->sub ?? null;

            // 4. Iniciar sesión manualmente en el Guard 'web'
            // Esto permite que auth()->user() funcione dentro del Widget
            if ($usuarioId) {
                // loginUsingId(id, remember = false)
                Auth::guard('web')->loginUsingId($usuarioId);
            }

        } catch (\Throwable $e) {
            // Si el token es inválido, no hacemos login. 
            // Opcional: Podríamos abortar con 401 aquí si queremos ser estrictos.
            // Por ahora, dejamos que fluya, el usuario será guest.
        }

        return $next($request);
    }

    private function getTokenFromRequest(Request $request): ?string
    {
        // A) Query Param "token" (ej: /helpdesk?token=XYZ) - Prioridad para iframes
        if ($token = $request->query('token')) {
            return $token;
        }

        // B) Header Authorization: Bearer <token>
        $header = $request->header('Authorization', '');
        if (str_starts_with($header, 'Bearer ')) {
            return substr($header, 7);
        }

        // C) Cookie (si aplica)
        return $request->cookie('authToken');
    }
}
