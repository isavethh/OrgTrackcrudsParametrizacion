<?php

namespace App\Http\Middleware;

use Closure;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
	/**
	 * Handle an incoming request.
	 */
	public function handle(Request $request, Closure $next): Response
	{
		// 1) Intentar Authorization: Bearer <token>
		$authHeader = $request->header('Authorization', '');
		$token = null;
		if (str_starts_with($authHeader, 'Bearer ')) {
			$token = substr($authHeader, 7);
		}
		// 2) Intentar header X-Auth-Token
		if (!$token) {
			$token = $request->header('X-Auth-Token');
		}
		// 3) Intentar query string ?token=
		if (!$token) {
			$token = $request->query('token');
		}
		// 4) Intentar cookie authToken (si frontend lo guarda ahí)
		if (!$token) {
			$token = $request->cookie('authToken');
		}
		if (!$token) {
			return response()->json(['error' => 'No autorizado (token faltante)'], 401);
		}

		$secret = env('SECRET_KEY') ?: env('JWT_SECRET');
		if (!$secret) {
			$secret = (string) config('app.key');
			if (str_starts_with($secret, 'base64:')) {
				$secret = base64_decode(substr($secret, 7));
			}
		}

		try {
			$payload = JWT::decode($token, new Key($secret, 'HS256'));
			$usuarioId = (int) ($payload->sub ?? 0);
			$rol = (string) ($payload->rol ?? '');
			if ($usuarioId <= 0) {
				return response()->json(['error' => 'Token inválido'], 401);
			}
			$request->attributes->set('usuario', [
				'id' => $usuarioId,
				'rol' => $rol ?: 'cliente',
			]);
		} catch (\Throwable $e) {
			return response()->json(['error' => 'Token inválido', 'detalle' => $e->getMessage()], 401);
		}

		return $next($request);
	}
}


