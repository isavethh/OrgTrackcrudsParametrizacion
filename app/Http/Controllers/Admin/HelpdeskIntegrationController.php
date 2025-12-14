<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class HelpdeskIntegrationController extends Controller
{
    /**
     * Devuelve la vista principal (contenedor del iframe).
     * Esta vista contendrá el JS para orquestar la carga.
     */
    public function index()
    {
        return view('admin.helpdesk.index');
    }

    /**
     * Endpoint API llamado por el frontend (JS) para obtener la URL firmada.
     * Recibe el Token JWT del usuario en el Header Authorization.
     */
    public function generateUrl(Request $request)
    {
        try {
            // 1. Obtener token del header
            $bearerToken = $request->bearerToken();
            if (!$bearerToken) {
                // Intento fallback: leer query param 'token' si viene
                $bearerToken = $request->query('token');
            }

            if (!$bearerToken) {
                return response()->json(['error' => 'No token provided'], 401);
            }

            // 2. Decodificar Token Manualmente (Nuclear Option)
            // No confiamos en Auth::user(), leemos el payload base64 directo.
            $tokenParts = explode('.', $bearerToken);
            if (count($tokenParts) < 2) {
                return response()->json(['error' => 'Invalid Token format'], 400);
            }

            $payloadBase64 = $tokenParts[1];
            // Fix base64 padding
            $padded = str_pad($payloadBase64, strlen($payloadBase64) % 4, '=', STR_PAD_RIGHT);
            $payloadJson = base64_decode(strtr($padded, '-_', '+/'));
            $payload = json_decode($payloadJson, true);

            if (!$payload) {
                return response()->json(['error' => 'Could not decode Token'], 400);
            }

            // EXTRAER DATOS DEL USUARIO DEL TOKEN
            // Ajusta estas claves según la estructura real de tu JWT
            // Usualmente 'sub' es el ID, y a veces traen 'email' o 'user' object.
            
            // Log para debug
            Log::info('Helpdesk Integration - Decoded JWT:', $payload);

            $userId = $payload['sub'] ?? null;
            
            // Si el token tiene el objeto 'user' dentro (común en algunos setups)
            $userEmail = $payload['user']['email'] ?? $payload['email'] ?? null;
            $firstName = $payload['user']['firstName'] ?? $payload['first_name'] ?? $payload['name'] ?? 'Usuario';
            $lastName  = $payload['user']['lastName'] ?? $payload['last_name'] ?? '';

            // SI NO HAY EMAIL EN EL TOKEN: Tenemos que buscarlo en BD usando el ID
            if (!$userEmail && $userId) {
                $dbUser = \App\Models\Usuario::find($userId);
                if ($dbUser) {
                    $userEmail = $dbUser->email; // Usando el accessor que arreglamos hoy
                    $firstName = $dbUser->first_name;
                    $lastName  = $dbUser->last_name;
                }
            }

            if (!$userEmail) {
                return response()->json(['error' => 'User Email not found in Token or DB'], 404);
            }

            // 3. Comunicarse con API Helpdesk (Server-to-Server)
            $apiKey = config('helpdeskwidget.api_key') ?? env('HELPDESK_API_KEY');
            $apiUrl = config('helpdeskwidget.api_url') ?? env('HELPDESK_API_URL');
            
            // Validar config
            if (!$apiKey || !$apiUrl) {
                return response()->json(['error' => 'Helpdesk Configuration missing'], 500);
            }

            // A) Check User / Login Trusted
            // Llamamos a /api/external/login directamente (Trusted Login)
            $response = Http::withHeaders([
                'X-Service-Key' => $apiKey,
                'Accept' => 'application/json'
            ])->post($apiUrl . '/api/external/login', [
                'email' => $userEmail
            ]);

            // Si falla porque no existe el usuario, intentar registrarlo o check-user
            if (!$response->successful()) {
                 // Si es 401 User Not Found -> Quizás llamar a check-user primero?
                 // Pero según docs, login trusted falla si usuario no existe.
                 // Podríamos intentar crear el usuario o asumir que debe crearse.
                 
                 // Intento B: Si falla login, hacer Check para ver si existe
                 $checkRes = Http::withHeaders(['X-Service-Key' => $apiKey])
                     ->post($apiUrl . '/api/external/check-user', ['email' => $userEmail]);
                     
                 if ($checkRes->successful() && !$checkRes->json('exists')) {
                     // El usuario no existe en Helpdesk.
                     // OPCIÓN: Registrarlo automáticamente o mandar a registro.
                     // Para la integración fluida, lo ideal sería registrarlo silenciosamente si la API externa lo permite.
                     // Docs dicen: POST /api/external/register
                     
                     $generatedPassword = \Illuminate\Support\Str::random(16) . 'A1!'; // Forzar complejidad si es necesario
                     
                     $registerRes = Http::withHeaders(['X-Service-Key' => $apiKey])
                        ->post($apiUrl . '/api/external/register', [
                            'email' => $userEmail,
                            'firstName' => $firstName,
                            'lastName' => $lastName,
                            'password' => $generatedPassword,
                            'password_confirmation' => $generatedPassword
                        ]);
                        
                     if ($registerRes->successful()) {
                         // Ahora sí tenemos token
                         $widgetAccessToken = $registerRes->json('accessToken');
                     } else {
                         return response()->json(['error' => 'Failed to auto-register user in Helpdesk', 'details' => $registerRes->json()], 500);
                     }
                 } else {
                     return response()->json(['error' => 'Helpdesk Login Failed', 'details' => $response->json()], 500);
                 }
            } else {
                $widgetAccessToken = $response->json('accessToken');
            }

            if (empty($widgetAccessToken)) {
                 return response()->json(['error' => 'No Access Token returned from Helpdesk'], 500);
            }

            // 4. Construir URL Final del Widget
            // La URL suele ser algo como: https://helpdesk.com/widget?token=...
            // Ojo: Depende de cómo el paquete widget construye la URL.
            // Si miramos el paquete, suele ser: $apiUrl . '/widget?token=' . $token . ...
            
            $finalUrl = rtrim($apiUrl, '/') . '/widget?token=' . $widgetAccessToken;
            
            // Agregar datos extra para UX si el widget los soporta
            $finalUrl .= '&email=' . urlencode($userEmail); 
            
            return response()->json([
                'success' => true,
                'iframeUrl' => $finalUrl
            ]);

        } catch (\Exception $e) {
            Log::error('Helpdesk Integration Error: ' . $e->getMessage());
            return response()->json(['error' => 'Internal Server Error: ' . $e->getMessage()], 500);
        }
    }
}
