<?php

return [
    // Dashboard
    [
        'text' => 'Dashboard',
        'route' => 'dashboard',
        'icon' => 'fas fa-tachometer-alt',
    ],
    
    // Envíos
    ['header' => 'GESTIÓN DE ENVÍOS'],
    [
        'text' => 'Mis Envíos',
        'route' => 'envios.index',
        'icon' => 'fas fa-shipping-fast',
    ],
    [
        'text' => 'Nuevo Envío',
        'route' => 'envios.create',
        'icon' => 'fas fa-plus-circle',
    ],
    
    // Direcciones
    ['header' => 'DIRECCIONES'],
    [
        'text' => 'Direcciones',
        'route' => 'direcciones.index',
        'icon' => 'fas fa-map-marked-alt',
    ],
    
    // Documentos
    ['header' => 'DOCUMENTOS'],
    [
        'text' => 'Mis Documentos',
        'route' => 'documentos.index',
        'icon' => 'fas fa-file-alt',
    ],
    
    // Logout
    ['header' => 'CUENTA'],
    [
        'text' => 'Cerrar Sesión',
        'icon' => 'fas fa-sign-out-alt',
        'url' => '#',
        'id' => 'logout-link',
    ],
];
