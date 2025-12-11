<?php

return [
    // Dashboard
    [
        'text' => 'Inicio',
        'route' => 'admin.dashboard',
        'icon' => 'fas fa-home',
    ],

    // --- ENVÍOS ---
    ['header' => 'ENVÍOS'],
    [
        'text' => 'Crear Nuevo Envío',
        'route' => 'admin.envios.create',
        'icon' => 'fas fa-plus-circle',
    ],
    [
        'text' => 'Ver Envíos',
        'route' => 'admin.envios.index',
        'icon' => 'fas fa-list',
    ],
    [
        'text' => 'Documentos',
        'route' => 'admin.documentos.index',
        'icon' => 'fas fa-file-alt',
    ],

    // --- USUARIOS ---
    ['header' => 'USUARIOS'],
    [
        'text' => 'Usuarios',
        'route' => 'admin.usuarios.index',
        'icon' => 'fas fa-users',
    ],

    // --- DIRECCIONES ---
    ['header' => 'DIRECCIONES'],
    [
        'text' => 'Direcciones',
        'route' => 'admin.direcciones.index',
        'icon' => 'fas fa-map-marker-alt',
    ],

    // --- FLOTA ---
    ['header' => 'GESTIÓN DE FLOTA'],
    [
        'text' => 'Transportistas',
        'route' => 'admin.transportistas.index',
        'icon' => 'fas fa-user-tie',
    ],
    [
        'text' => 'Vehículos',
        'route' => 'admin.vehiculos.index',
        'icon' => 'fas fa-truck',
    ],
    [
        'text' => 'Tipos de Vehículo',
        'route' => 'admin.tipos_vehiculo.index',
        'icon' => 'fas fa-truck-pickup',
    ],
    [
        'text' => 'Tipos de Transporte',
        'route' => 'admin.tipos_transporte.index',
        'icon' => 'fas fa-route',
    ],

    // --- CATÁLOGOS ---
    ['header' => 'CATÁLOGOS'],
    [
        'text' => 'Carga y Productos',
        'icon' => 'fas fa-boxes',
        'submenu' => [
            [
                'text' => 'Categorías',
                'route' => 'admin.categorias.index',
                'icon' => 'fas fa-tags',
            ],
            [
                'text' => 'Productos',
                'route' => 'admin.productos.index',
                'icon' => 'fas fa-apple-alt',
            ],
            [
                'text' => 'Tipos de Empaque',
                'route' => 'admin.tipos_empaque.index',
                'icon' => 'fas fa-box-open',
            ],
            [
                'text' => 'Tamaño / Conteo',
                'route' => 'admin.tamano_conteo.index',
                'icon' => 'fas fa-ruler-combined',
            ],

        ]
    ],
    [
        'text' => 'Calidad y Servicio',
        'icon' => 'fas fa-clipboard-check',
        'submenu' => [
            [
                'text' => 'Condiciones',
                'route' => 'admin.condiciones.index',
                'icon' => 'fas fa-check-double',
            ],
            [
                'text' => 'Incidentes',
                'route' => 'admin.incidentes.index',
                'icon' => 'fas fa-exclamation-circle',
            ],
        ]
    ],


    // --- REPORTES ---
    ['header' => 'REPORTES'],
    [
        'text' => 'Reportes Avanzados',
        'route' => 'admin.reportes.index',
        'icon' => 'fas fa-chart-line',
    ],

    // Logout
    ['header' => 'MI CUENTA'],
    [
        'text' => 'Cerrar Sesión',
        'icon' => 'fas fa-sign-out-alt',
        'url' => '#',
        'id' => 'logout-link',
    ],
];
