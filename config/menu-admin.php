<?php

return [
    // Dashboard
    [
        'text' => 'Dashboard',
        'route' => 'admin.dashboard',
        'icon' => 'fas fa-tachometer-alt',
    ],
    
    // Envíos
    ['header' => 'GESTIÓN DE ENVÍOS'],
    [
        'text' => 'Envíos',
        'route' => 'admin.envios.index',
        'icon' => 'fas fa-shipping-fast',
    ],
    [
        'text' => 'Nuevo Envío',
        'route' => 'admin.envios.create',
        'icon' => 'fas fa-plus-circle',
    ],
    
    // Usuarios
    ['header' => 'GESTIÓN DE USUARIOS'],
    [
        'text' => 'Usuarios',
        'route' => 'admin.usuarios.index',
        'icon' => 'fas fa-users',
    ],
    [
        'text' => 'Transportistas',
        'route' => 'admin.transportistas.index',
        'icon' => 'fas fa-user-friends',
    ],
    [
        'text' => 'Vehículos',
        'route' => 'admin.vehiculos.index',
        'icon' => 'fas fa-truck',
    ],
    
    // Direcciones y Documentos
    ['header' => 'DATOS'],
    [
        'text' => 'Direcciones',
        'route' => 'admin.direcciones.index',
        'icon' => 'fas fa-map-marked-alt',
    ],
    [
        'text' => 'Documentos',
        'route' => 'admin.documentos.index',
        'icon' => 'fas fa-file-alt',
    ],
    
    // Catálogos
    ['header' => 'CATÁLOGOS'],
    [
        'text' => 'Condiciones Transporte',
        'route' => 'admin.condiciones.index',
        'icon' => 'fas fa-check-circle',
    ],
    [
        'text' => 'Incidentes',
        'route' => 'admin.incidentes.index',
        'icon' => 'fas fa-exclamation-triangle',
    ],
    [
        'text' => 'Unidades Medida',
        'route' => 'admin.unidades_medida.index',
        'icon' => 'fas fa-ruler',
    ],
    [
        'text' => 'Catálogo Carga',
        'route' => 'admin.catalogo_carga.index',
        'icon' => 'fas fa-boxes',
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
