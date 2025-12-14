<?php

namespace App\View\Components;

use Lukehowland\HelpdeskWidget\View\Components\HelpdeskWidget;
use Illuminate\Support\Facades\Log;

class CustomHelpdeskWidget extends HelpdeskWidget
{
    /**
     * Override para mapear correctamente el nombre.
     */
    protected function getUserFirstName($user): string
    {
        // Debug para confirmar que llegamos aquí y qué usuario tenemos
        if (!$user) Log::error('CustomHelpdeskWidget: Usuario es NULL');
        else Log::info('CustomHelpdeskWidget: Procesando usuario ID ' . $user->id);

        // Tu lógica específica según tu modelo Usuario -> Persona
        if ($user->persona) {
            return $user->persona->nombre; 
        }
        
        // Fallback si no hay persona asociada
        return 'Usuario';
    }
    
    /**
     * Override para mapear correctamente el apellido.
     */
    protected function getUserLastName($user): string
    {
        if ($user->persona) {
            return $user->persona->apellido;
        }

        return '';
    }
}
