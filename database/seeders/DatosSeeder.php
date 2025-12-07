<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * 
     * php artisan db:seed --class=DatosSeeder
     * 
     */
    public function run(): void
    {
        $this->command->info('Iniciando seeder de datos de prueba...');

        // 1. Tipos de vehículos
        $this->seedTiposVehiculo();

        // 2. Tipos de transporte
        $this->seedTipoTransporte();

        // 3. Condiciones de transporte
        $this->seedCondicionesTransporte();

        // 4. Tipos de incidente
        $this->seedTiposIncidente();

        // 5. Catálogo de carga
        $this->seedCatalogoCarga();

        // 6. Clientes
        $clientesIds = $this->seedClientes();

        // 7. Transportistas
        $transportistasIds = $this->seedTransportistas();

        // 8. Vehículos
        $vehiculosIds = $this->seedVehiculos();

        // 9. Envíos con direcciones
        $this->seedEnvios($clientesIds, $transportistasIds, $vehiculosIds);

        // 10. Notificaciones
        $this->seedNotificaciones($clientesIds);

        // 11. Calificaciones
        $this->seedCalificaciones($clientesIds, $transportistasIds);

        $this->command->info('');
        $this->command->info('✅ Datos de prueba creados exitosamente!');
    }

    private function seedTiposVehiculo()
    {
        $tipos = [
            ['nombre' => 'Camión', 'descripcion' => 'Vehículo de carga pesada para transporte de productos agrícolas'],
            ['nombre' => 'Camioneta', 'descripcion' => 'Vehículo de carga mediana para entregas regionales'],
            ['nombre' => 'Furgoneta', 'descripcion' => 'Vehículo de carga ligera con refrigeración'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipos_vehiculo')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('  Tipos de vehículo');
    }

    private function seedTipoTransporte()
    {
        $tipos = [
            ['nombre' => 'Refrigerado', 'descripcion' => 'Ideal para frutas, verduras frescas, lácteos orgánicos y productos sensibles al calor.'],
            ['nombre' => 'Isotérmico', 'descripcion' => 'Adecuado para granos, productos secos, empaquetados y alimentos orgánicos que solo requieren protección térmica básica.'],
            ['nombre' => 'Multitemperatura', 'descripcion' => 'Perfecto para cargas mixtas: verduras frescas, carnes orgánicas, lácteos y productos secos en un mismo viaje.'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tipotransporte')->updateOrInsert(
                ['nombre' => $tipo['nombre']],
                $tipo
            );
        }

        $this->command->info('  ✓ Tipos de transporte');
    }

    private function seedCondicionesTransporte()
    {
        $condiciones = [
            ['codigo' => 'COND001', 'titulo' => 'Luces delanteras', 'descripcion' => 'Verificar funcionamiento de luces delanteras'],
            ['codigo' => 'COND002', 'titulo' => 'Luces traseras', 'descripcion' => 'Verificar funcionamiento de luces traseras'],
            ['codigo' => 'COND003', 'titulo' => 'Neumáticos', 'descripcion' => 'Verificar estado y presión de neumáticos'],
            ['codigo' => 'COND004', 'titulo' => 'Frenos', 'descripcion' => 'Verificar sistema de frenos'],
            ['codigo' => 'COND005', 'titulo' => 'Limpieza interior', 'descripcion' => 'Verificar limpieza del área de carga'],
            ['codigo' => 'COND006', 'titulo' => 'Documentación', 'descripcion' => 'Verificar documentos del vehículo vigentes'],
            ['codigo' => 'COND007', 'titulo' => 'Combustible', 'descripcion' => 'Verificar nivel de combustible adecuado'],
            ['codigo' => 'COND008', 'titulo' => 'Sistema de refrigeración', 'descripcion' => 'Verificar temperatura si aplica'],
        ];

        foreach ($condiciones as $condicion) {
            DB::table('condiciones_transporte')->updateOrInsert(
                ['codigo' => $condicion['codigo']],
                $condicion
            );
        }

        $this->command->info('  ✓ Condiciones de transporte');
    }

    private function seedTiposIncidente()
    {
        $incidentes = [
            ['codigo' => 'INC001', 'titulo' => 'Accidente de tráfico', 'descripcion' => 'Colisión o accidente vial'],
            ['codigo' => 'INC002', 'titulo' => 'Falla mecánica', 'descripcion' => 'Desperfecto mecánico del vehículo'],
            ['codigo' => 'INC003', 'titulo' => 'Retraso en tráfico', 'descripcion' => 'Demora por congestión vehicular'],
            ['codigo' => 'INC004', 'titulo' => 'Condiciones climáticas', 'descripcion' => 'Demora por mal tiempo'],
            ['codigo' => 'INC005', 'titulo' => 'Daño a la carga', 'descripcion' => 'Deterioro o daño del producto'],
            ['codigo' => 'INC006', 'titulo' => 'Robo o extravío', 'descripcion' => 'Pérdida parcial o total de la carga'],
            ['codigo' => 'INC007', 'titulo' => 'Bloqueo de carretera', 'descripcion' => 'Vía cerrada o bloqueada'],
        ];

        foreach ($incidentes as $incidente) {
            DB::table('tipos_incidente_transporte')->updateOrInsert(
                ['codigo' => $incidente['codigo']],
                $incidente
            );
        }

        $this->command->info('  ✓ Tipos de incidente');
    }

    private function seedCatalogoCarga()
    {
        $cargas = [
            // Frutas orgánicas
            ['tipo' => 'Frutas', 'variedad' => 'Manzanas', 'empaque' => 'Cajas de cartón 10kg', 'descripcion' => 'Manzanas orgánicas certificadas'],
            ['tipo' => 'Frutas', 'variedad' => 'Naranjas', 'empaque' => 'Cajas de cartón 15kg', 'descripcion' => 'Naranjas orgánicas frescas'],
            ['tipo' => 'Frutas', 'variedad' => 'Plátanos', 'empaque' => 'Cajas ventiladas 12kg', 'descripcion' => 'Plátanos orgánicos maduración controlada'],
            
            // Verduras orgánicas
            ['tipo' => 'Verduras', 'variedad' => 'Lechugas', 'empaque' => 'Cajas refrigeradas 5kg', 'descripcion' => 'Lechugas orgánicas hidropónicas'],
            ['tipo' => 'Verduras', 'variedad' => 'Tomates', 'empaque' => 'Cajas de cartón 8kg', 'descripcion' => 'Tomates orgánicos cherry y ensalada'],
            ['tipo' => 'Verduras', 'variedad' => 'Zanahorias', 'empaque' => 'Sacos de malla 20kg', 'descripcion' => 'Zanahorias orgánicas seleccionadas'],
            
            // Granos orgánicos
            ['tipo' => 'Granos', 'variedad' => 'Quinua', 'empaque' => 'Sacos de yute 25kg', 'descripcion' => 'Quinua orgánica real certificada'],
            ['tipo' => 'Granos', 'variedad' => 'Amaranto', 'empaque' => 'Sacos de yute 20kg', 'descripcion' => 'Amaranto orgánico premium'],
            
            // Lácteos orgánicos
            ['tipo' => 'Lácteos', 'variedad' => 'Leche fresca', 'empaque' => 'Bidones térmicos 50L', 'descripcion' => 'Leche orgánica pasteurizada'],
            ['tipo' => 'Lácteos', 'variedad' => 'Quesos', 'empaque' => 'Cajas refrigeradas 10kg', 'descripcion' => 'Quesos orgánicos artesanales'],
        ];

        foreach ($cargas as $carga) {
            DB::table('catalogo_carga')->insert($carga);
        }

        $this->command->info('  ✓ Catálogo de carga (productos agrícolas orgánicos)');
    }

    private function seedClientes()
    {
        $rolCliente = DB::table('roles_usuario')->where('codigo', 'cliente')->first();

        $clientes = [
            ['nombre' => 'María', 'apellido' => 'González', 'ci' => '7845321', 'telefono' => '71234567', 'correo' => 'maria@gmail.com', 'contrasena' => 'maria123'],
            ['nombre' => 'Pedro', 'apellido' => 'Rodríguez', 'ci' => '8956432', 'telefono' => '72345678', 'correo' => 'pedro@gmail.com', 'contrasena' => 'pedro123'],
            ['nombre' => 'Ana', 'apellido' => 'López', 'ci' => '9067543', 'telefono' => '73456789', 'correo' => 'ana@gmail.com', 'contrasena' => 'ana123'],
            ['nombre' => 'Jorge', 'apellido' => 'Fernández', 'ci' => '7178654', 'telefono' => '74567890', 'correo' => 'jorge@gmail.com', 'contrasena' => 'jorge123'],
            ['nombre' => 'Laura', 'apellido' => 'Sánchez', 'ci' => '8289765', 'telefono' => '75678901', 'correo' => 'laura@gmail.com', 'contrasena' => 'laura123'],
        ];

        $clientesIds = [];

        foreach ($clientes as $clienteData) {
            // Verificar si ya existe
            $usuarioExistente = DB::table('usuarios')->where('correo', $clienteData['correo'])->first();
            
            if ($usuarioExistente) {
                $clientesIds[] = $usuarioExistente->id;
                continue;
            }

            DB::transaction(function () use ($clienteData, $rolCliente, &$clientesIds) {
                // Crear persona
                $idPersona = DB::table('persona')->insertGetId([
                    'nombre' => $clienteData['nombre'],
                    'apellido' => $clienteData['apellido'],
                    'ci' => $clienteData['ci'],
                    'telefono' => $clienteData['telefono'],
                ]);

                // Crear usuario
                $idUsuario = DB::table('usuarios')->insertGetId([
                    'correo' => $clienteData['correo'],
                    'contrasena' => Hash::make($clienteData['contrasena']),
                    'id_rol' => $rolCliente->id,
                    'fecha_registro' => now(),
                    'id_persona' => $idPersona,
                ]);

                // Crear cliente
                DB::table('cliente')->insert([
                    'id_usuario' => $idUsuario,
                ]);

                $clientesIds[] = $idUsuario;
            });
        }

        $this->command->info('  ✓ Clientes (5) - Productores');
        return $clientesIds;
    }

    private function seedTransportistas()
    {
        $rolTransportista = DB::table('roles_usuario')->where('codigo', 'transportista')->first();
        $estadoDisponible = DB::table('estados_transportista')->where('nombre', 'Disponible')->first();

        $transportistas = [
            ['nombre' => 'Juan', 'apellido' => 'Pérez', 'ci' => '6734512', 'telefono' => '76123456', 'correo' => 'juan@transporte.com', 'contrasena' => 'juan123'],
            ['nombre' => 'Roberto', 'apellido' => 'Vargas', 'ci' => '7845623', 'telefono' => '77234567', 'correo' => 'roberto@transporte.com', 'contrasena' => 'roberto123'],
            ['nombre' => 'Miguel', 'apellido' => 'Torres', 'ci' => '8956734', 'telefono' => '78345678', 'correo' => 'miguel@transporte.com', 'contrasena' => 'miguel123'],
            ['nombre' => 'Carlos', 'apellido' => 'Quispe', 'ci' => '9067845', 'telefono' => '79456789', 'correo' => 'carlos.t@transporte.com', 'contrasena' => 'carlos123'],
            ['nombre' => 'Luis', 'apellido' => 'Mamani', 'ci' => '1178956', 'telefono' => '70567890', 'correo' => 'luis@transporte.com', 'contrasena' => 'luis123'],
            ['nombre' => 'Fernando', 'apellido' => 'Condori', 'ci' => '2289067', 'telefono' => '71678901', 'correo' => 'fernando@transporte.com', 'contrasena' => 'fernando123'],
            ['nombre' => 'Ricardo', 'apellido' => 'Choque', 'ci' => '3390178', 'telefono' => '72789012', 'correo' => 'ricardo@transporte.com', 'contrasena' => 'ricardo123'],
            ['nombre' => 'Daniel', 'apellido' => 'Ayala', 'ci' => '4401289', 'telefono' => '73890123', 'correo' => 'daniel@transporte.com', 'contrasena' => 'daniel123'],
            ['nombre' => 'Sergio', 'apellido' => 'Rojas', 'ci' => '5512390', 'telefono' => '74901234', 'correo' => 'sergio@transporte.com', 'contrasena' => 'sergio123'],
            ['nombre' => 'Pablo', 'apellido' => 'Gutierrez', 'ci' => '6623401', 'telefono' => '75012345', 'correo' => 'pablo@transporte.com', 'contrasena' => 'pablo123'],
        ];

        $transportistasIds = [];

        foreach ($transportistas as $transportistaData) {
            // Verificar si ya existe
            $usuarioExistente = DB::table('usuarios')->where('correo', $transportistaData['correo'])->first();
            
            if ($usuarioExistente) {
                // Obtener el ID del transportista
                $transportista = DB::table('transportistas')->where('id_usuario', $usuarioExistente->id)->first();
                if ($transportista) {
                    $transportistasIds[] = $transportista->id;
                }
                continue;
            }

            DB::transaction(function () use ($transportistaData, $rolTransportista, $estadoDisponible, &$transportistasIds) {
                // Crear persona
                $idPersona = DB::table('persona')->insertGetId([
                    'nombre' => $transportistaData['nombre'],
                    'apellido' => $transportistaData['apellido'],
                    'ci' => $transportistaData['ci'],
                    'telefono' => $transportistaData['telefono'],
                ]);

                // Crear usuario
                $idUsuario = DB::table('usuarios')->insertGetId([
                    'correo' => $transportistaData['correo'],
                    'contrasena' => Hash::make($transportistaData['contrasena']),
                    'id_rol' => $rolTransportista->id,
                    'fecha_registro' => now(),
                    'id_persona' => $idPersona,
                ]);

                // Crear transportista
                $idTransportista = DB::table('transportistas')->insertGetId([
                    'id_usuario' => $idUsuario,
                    'id_estado_transportista' => $estadoDisponible->id,
                    'fecha_registro' => now(),
                ]);

                $transportistasIds[] = $idTransportista;
            });
        }

        $this->command->info('  ✓ Transportistas (10)');
        return $transportistasIds;
    }

    private function seedVehiculos()
    {
        $tipoCamion = DB::table('tipos_vehiculo')->where('nombre', 'Camión')->first();
        $tipoCamioneta = DB::table('tipos_vehiculo')->where('nombre', 'Camioneta')->first();
        $tipoFurgoneta = DB::table('tipos_vehiculo')->where('nombre', 'Furgoneta')->first();
        $estadoDisponible = DB::table('estados_vehiculo')->where('nombre', 'Disponible')->first();
        $tipoRefrigerado = DB::table('tipotransporte')->where('nombre', 'Refrigerado')->first();
        $tipoIsotermico = DB::table('tipotransporte')->where('nombre', 'Isotérmico')->first();

        $vehiculos = [
            ['id_tipo_vehiculo' => $tipoCamion->id, 'placa' => 'SCZ-1234', 'capacidad' => 5000.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
            ['id_tipo_vehiculo' => $tipoCamion->id, 'placa' => 'LPZ-5678', 'capacidad' => 4500.00, 'id_tipo_transporte' => $tipoIsotermico->id],
            ['id_tipo_vehiculo' => $tipoCamioneta->id, 'placa' => 'CBB-9012', 'capacidad' => 2000.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
            ['id_tipo_vehiculo' => $tipoCamioneta->id, 'placa' => 'TJA-3456', 'capacidad' => 1800.00, 'id_tipo_transporte' => $tipoIsotermico->id],
            ['id_tipo_vehiculo' => $tipoFurgoneta->id, 'placa' => 'ORU-7890', 'capacidad' => 1000.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
            ['id_tipo_vehiculo' => $tipoCamion->id, 'placa' => 'PTI-1111', 'capacidad' => 4800.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
            ['id_tipo_vehiculo' => $tipoCamion->id, 'placa' => 'BNI-2222', 'capacidad' => 5200.00, 'id_tipo_transporte' => $tipoIsotermico->id],
            ['id_tipo_vehiculo' => $tipoCamioneta->id, 'placa' => 'PND-3333', 'capacidad' => 2100.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
            ['id_tipo_vehiculo' => $tipoCamioneta->id, 'placa' => 'CHQ-4444', 'capacidad' => 1900.00, 'id_tipo_transporte' => $tipoIsotermico->id],
            ['id_tipo_vehiculo' => $tipoFurgoneta->id, 'placa' => 'SCZ-5555', 'capacidad' => 1100.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
        ];

        $vehiculosIds = [];

        foreach ($vehiculos as $vehiculo) {
            // Verificar si ya existe
            $vehiculoExistente = DB::table('vehiculos')->where('placa', $vehiculo['placa'])->first();
            
            if ($vehiculoExistente) {
                $vehiculosIds[] = $vehiculoExistente->id;
                continue;
            }

            $id = DB::table('vehiculos')->insertGetId([
                'id_tipo_vehiculo' => $vehiculo['id_tipo_vehiculo'],
                'id_tipo_transporte' => $vehiculo['id_tipo_transporte'],
                'placa' => $vehiculo['placa'],
                'capacidad' => $vehiculo['capacidad'],
                'id_estado_vehiculo' => $estadoDisponible->id,
                'fecha_registro' => now(),
            ]);

            $vehiculosIds[] = $id;
        }

        $this->command->info('  ✓ Vehículos (10) - Todos Disponibles');
        return $vehiculosIds;
    }

    private function seedEnvios($clientesIds, $transportistasIds, $vehiculosIds)
    {
        if (empty($clientesIds) || empty($transportistasIds) || empty($vehiculosIds)) {
            $this->command->warn('  ⚠ No hay suficientes datos para crear envíos');
            return;
        }

        // Estados de envío
        $estadoPendiente = DB::table('estados_envio')->where('nombre', 'Pendiente')->first();
        $estadoAsignado = DB::table('estados_envio')->where('nombre', 'Asignado')->first();
        $estadoEnCurso = DB::table('estados_envio')->where('nombre', 'En curso')->first();
        $estadoEntregado = DB::table('estados_envio')->where('nombre', 'Entregado')->first();
        
        // Estados de asignación
        $estadoAsigPendiente = DB::table('estados_asignacion_multiple')->where('nombre', 'Pendiente')->first();
        $estadoAsigEnCurso = DB::table('estados_asignacion_multiple')->where('nombre', 'En curso')->first();
        $estadoAsigCompletada = DB::table('estados_asignacion_multiple')->where('nombre', 'Completada')->first();
        
        // Estados de transportista y vehículo
        $estadoTranspDisponible = DB::table('estados_transportista')->where('nombre', 'Disponible')->first();
        $estadoTranspNoDisponible = DB::table('estados_transportista')->where('nombre', 'No Disponible')->first();
        $estadoVehDisponible = DB::table('estados_vehiculo')->where('nombre', 'Disponible')->first();
        $estadoVehNoDisponible = DB::table('estados_vehiculo')->where('nombre', 'No Disponible')->first();
        
        // Tipos de transporte
        $tipoRefrigerado = DB::table('tipotransporte')->where('nombre', 'Refrigerado')->first();
        $tipoIsotermico = DB::table('tipotransporte')->where('nombre', 'Isotérmico')->first();
        $tipoMultitemp = DB::table('tipotransporte')->where('nombre', 'Multitemperatura')->first();
        
        // Catálogos de carga
        $catalogoFrutas = DB::table('catalogo_carga')->where('tipo', 'Frutas')->where('variedad', 'Manzanas')->first();
        $catalogoVerduras = DB::table('catalogo_carga')->where('tipo', 'Verduras')->where('variedad', 'Lechugas')->first();
        $catalogoGranos = DB::table('catalogo_carga')->where('tipo', 'Granos')->where('variedad', 'Quinua')->first();
        $catalogoNaranjas = DB::table('catalogo_carga')->where('tipo', 'Frutas')->where('variedad', 'Naranjas')->first();
        $catalogoTomates = DB::table('catalogo_carga')->where('tipo', 'Verduras')->where('variedad', 'Tomates')->first();
        
        $condiciones = DB::table('condiciones_transporte')->limit(5)->get();

        // ========================================
        // Envío 1: COMPLETADO con documentos (transportista y vehículo liberados)
        // ========================================
        DB::transaction(function () use ($clientesIds, $transportistasIds, $vehiculosIds, $estadoPendiente, $estadoAsignado, $estadoEnCurso, $estadoEntregado, $estadoAsigCompletada, $estadoTranspDisponible, $estadoVehDisponible, $tipoRefrigerado, $catalogoFrutas, $condiciones) {
            // 1. Dirección: Productor Santa Cruz → Planta La Paz (Ruta realista por carretera)
            // Ruta: Warnes → Santa Cruz → Cochabamba → Oruro → El Alto → La Paz
            $rutaCompleta = [
                [-63.1812, -17.7833],  // Warnes (inicio)
                [-63.1500, -17.7500],  // Salida hacia carretera
                [-63.1700, -17.7000],  // Carretera principal
                [-63.5000, -17.5000],  // Hacia Montero
                [-64.0000, -17.4000],  // Zona intermedia
                [-64.7300, -17.4139],  // Yapacaní
                [-65.2500, -17.3900],  // Llegando a Cochabamba
                [-66.1568, -17.3936],  // Cochabamba (punto intermedio)
                [-66.5000, -17.5000],  // Salida Cochabamba
                [-67.0000, -17.8000],  // Carretera a Oruro
                [-67.1092, -17.9636],  // Oruro (punto intermedio)
                [-67.5000, -18.0000],  // Continuando
                [-68.0000, -17.5000],  // Acercándose a El Alto
                [-68.1500, -16.5500],  // El Alto
                [-68.1300, -16.5200],  // Bajada a La Paz
                [-68.1193, -16.5000]   // La Paz (destino)
            ];
            
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => $clientesIds[0],
                'nombreorigen' => 'Finca El Paraíso, Warnes - Santa Cruz',
                'origen_lng' => -63.1812,
                'origen_lat' => -17.7833,
                'nombredestino' => 'Planta Procesadora OrganiCo, Zona Sur - La Paz',
                'destino_lng' => -68.1193,
                'destino_lat' => -16.5000,
                'rutageojson' => json_encode([
                    'type' => 'LineString',
                    'coordinates' => $rutaCompleta
                ], JSON_UNESCAPED_SLASHES),
            ]);
            
            // Crear segmentos de la ruta (dividir en tramos principales)
            $segmentos = [
                // Segmento 1: Warnes → Cochabamba
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 0, 8),
                        'properties' => ['tramo' => 'Warnes - Cochabamba', 'distancia_km' => 520]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                // Segmento 2: Cochabamba → Oruro
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 7, 4),
                        'properties' => ['tramo' => 'Cochabamba - Oruro', 'distancia_km' => 204]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                // Segmento 3: Oruro → La Paz
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 10),
                        'properties' => ['tramo' => 'Oruro - La Paz', 'distancia_km' => 230]
                    ], JSON_UNESCAPED_SLASHES)
                ]
            ];
            
            foreach ($segmentos as $segmento) {
                DB::table('direccionsegmento')->insert($segmento);
            }

            // 2. Envío completado
            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[0],
                'fecha_creacion' => now()->subDays(10),
                'fecha_inicio' => now()->subDays(9),
                'fecha_entrega' => now()->subDays(7),
                'id_direccion' => $idDireccion,
            ]);

            // 3. Historial: Pendiente → Asignado → En curso → Entregado
            DB::table('historialestados')->insert([
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoPendiente->id, 'fecha' => now()->subDays(10)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoAsignado->id, 'fecha' => now()->subDays(10)->addHours(1)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoEnCurso->id, 'fecha' => now()->subDays(9)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoEntregado->id, 'fecha' => now()->subDays(7)],
            ]);

            // 4. Recogida/Entrega
            $idRecogida = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->subDays(9)->format('Y-m-d'),
                'hora_recogida' => '06:00:00',
                'hora_entrega' => '16:00:00',
                'instrucciones_recogida' => 'Contactar al capataz Sr. Flores. Carga refrigerada.',
                'instrucciones_entrega' => 'Entregar en muelle de recepción, zona refrigerada.',
            ]);

            // 5. Carga
            $idUnidadKG = DB::table('unidades_medida')->where('codigo', 'KG')->first()->id;
            $idCarga = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoFrutas->id,
                'cantidad' => 150,
                'peso' => 1500.00,
                'id_unidad_medida' => $idUnidadKG,
            ]);

            // 6. Asignación completada (GENERAR CÓDIGO DE ACCESO)
            $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));
            
            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => $transportistasIds[0],
                'id_vehiculo' => $vehiculosIds[0],
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoRefrigerado->id,
                'id_estado_asignacion' => $estadoAsigCompletada->id,
                'codigo_acceso' => $codigoAcceso,  // CÓDIGO DE ACCESO GENERADO
                'fecha_asignacion' => now()->subDays(10),
                'fecha_inicio' => now()->subDays(9),
                'fecha_fin' => now()->subDays(7),
            ]);
            
            // 7. Como el envío ya está completado, transportista y vehículo están DISPONIBLES nuevamente
            DB::table('transportistas')->where('id', $transportistasIds[0])->update(['id_estado_transportista' => $estadoTranspDisponible->id]);
            DB::table('vehiculos')->where('id', $vehiculosIds[0])->update(['id_estado_vehiculo' => $estadoVehDisponible->id]);

            // 7. Como el envío ya está completado, transportista y vehículo están DISPONIBLES nuevamente
            DB::table('transportistas')->where('id', $transportistasIds[0])->update(['id_estado_transportista' => $estadoTranspDisponible->id]);
            DB::table('vehiculos')->where('id', $vehiculosIds[0])->update(['id_estado_vehiculo' => $estadoVehDisponible->id]);

            // 8. Asociar carga
            DB::table('asignacioncarga')->insert([
                'id_asignacion' => $idAsignacion,
                'id_carga' => $idCarga,
            ]);

            // 9. Checklist completado
            $idChecklist = DB::table('checklist_condicion')->insertGetId([
                'id_asignacion' => $idAsignacion,
                'fecha' => now()->subDays(9),
                'observaciones' => 'Vehículo en óptimas condiciones',
            ]);

            foreach ($condiciones as $condicion) {
                DB::table('checklist_condicion_detalle')->insert([
                    'id_checklist' => $idChecklist,
                    'id_condicion' => $condicion->id,
                    'valor' => true,
                    'comentario' => 'Verificado OK',
                ]);
            }

            // 10. Firmas (base64 simplificado para prueba)
            $firmaBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            
            DB::table('firmaenvio')->insert([
                'id_asignacion' => $idAsignacion,
                'imagenfirma' => $firmaBase64,
                'fechafirma' => now()->subDays(7),
            ]);

            DB::table('firmatransportista')->insert([
                'id_asignacion' => $idAsignacion,
                'imagenfirma' => $firmaBase64,
                'fechafirma' => now()->subDays(9),
            ]);

            // 11. QR Token (usado)
            $estadoQrUsado = DB::table('estados_qrtoken')->where('nombre', 'Usado')->first();
            $token = 'ENV1-' . bin2hex(random_bytes(16));
            $qrBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            
            DB::table('qrtoken')->insert([
                'id_asignacion' => $idAsignacion,
                'id_estado_qrtoken' => $estadoQrUsado->id,
                'token' => $token,
                'imagenqr' => $qrBase64,
                'fecha_creacion' => now()->subDays(10),
                'fecha_expiracion' => now()->subDays(7)->addHours(2),
            ]);

            // 12. Checklist de incidentes (sin incidentes reportados)
            $tiposIncidente = DB::table('tipos_incidente_transporte')->limit(5)->get();
            $idChecklistIncidente = DB::table('checklist_incidente')->insertGetId([
                'id_asignacion' => $idAsignacion,
                'fecha' => now()->subDays(9),
                'observaciones' => 'Transporte sin incidentes',
            ]);

            foreach ($tiposIncidente as $tipoIncidente) {
                DB::table('checklist_incidente_detalle')->insert([
                    'id_checklist' => $idChecklistIncidente,
                    'id_tipo_incidente' => $tipoIncidente->id,
                    'ocurrio' => false,
                    'descripcion' => null,
                ]);
            }
        });

        // ========================================
        // Envío 2: EN CURSO (transportista y vehículo NO DISPONIBLES)
        // ========================================
        DB::transaction(function () use ($clientesIds, $transportistasIds, $vehiculosIds, $estadoAsignado, $estadoEnCurso, $estadoAsigEnCurso, $estadoTranspNoDisponible, $estadoVehNoDisponible, $tipoIsotermico, $catalogoGranos) {
            // Ruta: Challapata (Oruro) → La Paz por carretera principal
            $rutaCompleta = [
                [-66.7667, -18.9167],  // Challapata (inicio)
                [-66.8000, -18.8000],  // Salida de Challapata
                [-66.9000, -18.5000],  // Carretera hacia Oruro
                [-67.1092, -17.9636],  // Oruro (punto intermedio)
                [-67.3000, -17.7000],  // Continuando hacia norte
                [-67.7000, -17.3000],  // Zona intermedia
                [-68.0000, -17.0000],  // Acercándose a El Alto
                [-68.1500, -16.5500],  // El Alto
                [-68.1300, -16.5200],  // Bajada a La Paz
                [-68.1193, -16.5000]   // La Paz (destino)
            ];
            
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => $clientesIds[1],
                'nombreorigen' => 'Cooperativa Andina, Challapata - Oruro',
                'origen_lng' => -66.7667,
                'origen_lat' => -18.9167,
                'nombredestino' => 'Planta Procesadora OrganiCo, Zona Sur - La Paz',
                'destino_lng' => -68.1193,
                'destino_lat' => -16.5000,
                'rutageojson' => json_encode([
                    'type' => 'LineString',
                    'coordinates' => $rutaCompleta
                ], JSON_UNESCAPED_SLASHES),
            ]);
            
            // Crear segmentos
            $segmentos = [
                // Segmento 1: Challapata → Oruro
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 0, 4),
                        'properties' => ['tramo' => 'Challapata - Oruro', 'distancia_km' => 120]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                // Segmento 2: Oruro → La Paz
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 3),
                        'properties' => ['tramo' => 'Oruro - La Paz', 'distancia_km' => 230]
                    ], JSON_UNESCAPED_SLASHES)
                ]
            ];
            
            foreach ($segmentos as $segmento) {
                DB::table('direccionsegmento')->insert($segmento);
            }

            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[1],
                'fecha_creacion' => now()->subDays(3),
                'fecha_inicio' => now()->subDays(2),
                'fecha_entrega' => null,
                'id_direccion' => $idDireccion,
            ]);

            DB::table('historialestados')->insert([
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'Pendiente')->first()->id, 'fecha' => now()->subDays(3)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoAsignado->id, 'fecha' => now()->subDays(3)->addHours(1)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoEnCurso->id, 'fecha' => now()->subDays(2)],
            ]);

            $idRecogida = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->subDays(2)->format('Y-m-d'),
                'hora_recogida' => '07:00:00',
                'hora_entrega' => '18:00:00',
                'instrucciones_recogida' => 'Recoger en almacén principal de la cooperativa.',
                'instrucciones_entrega' => 'Descargar en almacén de granos secos.',
            ]);

            $idUnidadSACO = DB::table('unidades_medida')->where('codigo', 'SACO')->first()->id;
            $idCarga = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoGranos->id,
                'cantidad' => 80,
                'peso' => 2000.00,
                'id_unidad_medida' => $idUnidadSACO,
            ]);

            // GENERAR CÓDIGO DE ACCESO (crítico para envíos asignados)
            $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => $transportistasIds[1],
                'id_vehiculo' => $vehiculosIds[1],
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoIsotermico->id,
                'id_estado_asignacion' => $estadoAsigEnCurso->id,
                'codigo_acceso' => $codigoAcceso,  // CÓDIGO DE ACCESO GENERADO
                'fecha_asignacion' => now()->subDays(3),
                'fecha_inicio' => now()->subDays(2),
                'fecha_fin' => null,
            ]);
            
            // Marcar transportista y vehículo como NO DISPONIBLES
            DB::table('transportistas')->where('id', $transportistasIds[1])->update(['id_estado_transportista' => $estadoTranspNoDisponible->id]);
            DB::table('vehiculos')->where('id', $vehiculosIds[1])->update(['id_estado_vehiculo' => $estadoVehNoDisponible->id]);

            DB::table('asignacioncarga')->insert([
                'id_asignacion' => $idAsignacion,
                'id_carga' => $idCarga,
            ]);

            // Firma transportista solamente (aún no entregado)
            $firmaBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            
            DB::table('firmatransportista')->insert([
                'id_asignacion' => $idAsignacion,
                'imagenfirma' => $firmaBase64,
                'fechafirma' => now()->subDays(2),
            ]);

            // QR Token (activo, aún en curso)
            $estadoQrActivo = DB::table('estados_qrtoken')->where('nombre', 'Activo')->first();
            $token = 'ENV2-' . bin2hex(random_bytes(16));
            $qrBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            
            DB::table('qrtoken')->insert([
                'id_asignacion' => $idAsignacion,
                'id_estado_qrtoken' => $estadoQrActivo->id,
                'token' => $token,
                'imagenqr' => $qrBase64,
                'fecha_creacion' => now()->subDays(3),
                'fecha_expiracion' => now()->addDays(2),
            ]);
        });

        // ========================================
        // Envío 3: ASIGNADO (transportista y vehículo NO DISPONIBLES, pero aún no iniciado)
        // ========================================
        DB::transaction(function () use ($clientesIds, $transportistasIds, $vehiculosIds, $estadoAsignado, $estadoAsigPendiente, $estadoTranspNoDisponible, $estadoVehNoDisponible, $tipoRefrigerado, $catalogoVerduras) {
            // Ruta: Cochabamba → La Paz (ruta corta por carretera principal)
            $rutaCompleta = [
                [-66.2789, -17.3936],  // Cochabamba (inicio)
                [-66.5000, -17.5000],  // Salida hacia Oruro
                [-67.0000, -17.8000],  // Carretera principal
                [-67.1092, -17.9636],  // Oruro (punto intermedio)
                [-67.5000, -17.8000],  // Continuando
                [-68.0000, -17.2000],  // Zona intermedia
                [-68.1500, -16.5500],  // El Alto
                [-68.1300, -16.5200],  // Bajada a La Paz
                [-68.1193, -16.5000]   // La Paz (destino)
            ];
            
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => $clientesIds[2],
                'nombreorigen' => 'Cultivos Hidropónicos Verde Vida, Quillacollo - Cochabamba',
                'origen_lng' => -66.2789,
                'origen_lat' => -17.3936,
                'nombredestino' => 'Planta Procesadora OrganiCo, Zona Sur - La Paz',
                'destino_lng' => -68.1193,
                'destino_lat' => -16.5000,
                'rutageojson' => json_encode([
                    'type' => 'LineString',
                    'coordinates' => $rutaCompleta
                ], JSON_UNESCAPED_SLASHES),
            ]);
            
            // Crear segmentos
            $segmentos = [
                // Segmento 1: Cochabamba → Oruro
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 0, 4),
                        'properties' => ['tramo' => 'Cochabamba - Oruro', 'distancia_km' => 204]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                // Segmento 2: Oruro → La Paz
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 3),
                        'properties' => ['tramo' => 'Oruro - La Paz', 'distancia_km' => 230]
                    ], JSON_UNESCAPED_SLASHES)
                ]
            ];
            
            foreach ($segmentos as $segmento) {
                DB::table('direccionsegmento')->insert($segmento);
            }

            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[2],
                'fecha_creacion' => now()->subHours(6),
                'fecha_inicio' => null,
                'fecha_entrega' => null,
                'id_direccion' => $idDireccion,
            ]);

            // Estado: Pendiente → Asignado (tiene transportista y vehículo)
            DB::table('historialestados')->insert([
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'Pendiente')->first()->id, 'fecha' => now()->subHours(6)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoAsignado->id, 'fecha' => now()->subHours(5)],
            ]);

            $idRecogida = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->addDay()->format('Y-m-d'),
                'hora_recogida' => '05:00:00',
                'hora_entrega' => '14:00:00',
                'instrucciones_recogida' => 'Producto muy frágil. Mantener temperatura 2-4°C.',
                'instrucciones_entrega' => 'Entrega urgente en cámara refrigerada.',
            ]);

            $idUnidadKG = DB::table('unidades_medida')->where('codigo', 'KG')->first()->id;
            $idCarga = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoVerduras->id,
                'cantidad' => 200,
                'peso' => 1000.00,
                'id_unidad_medida' => $idUnidadKG,
            ]);

            // GENERAR CÓDIGO DE ACCESO (crítico para envíos asignados)
            $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => $transportistasIds[2],
                'id_vehiculo' => $vehiculosIds[2],
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoRefrigerado->id,
                'id_estado_asignacion' => $estadoAsigPendiente->id,  // Pendiente porque aún no ha iniciado el viaje
                'codigo_acceso' => $codigoAcceso,  // CÓDIGO DE ACCESO GENERADO
                'fecha_asignacion' => now()->subHours(5),
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ]);
            
            // Marcar transportista y vehículo como NO DISPONIBLES (ya están asignados)
            DB::table('transportistas')->where('id', $transportistasIds[2])->update(['id_estado_transportista' => $estadoTranspNoDisponible->id]);
            DB::table('vehiculos')->where('id', $vehiculosIds[2])->update(['id_estado_vehiculo' => $estadoVehNoDisponible->id]);

            DB::table('asignacioncarga')->insert([
                'id_asignacion' => $idAsignacion,
                'id_carga' => $idCarga,
            ]);

            // QR Token (activo, recién creado)
            $estadoQrActivo = DB::table('estados_qrtoken')->where('nombre', 'Activo')->first();
            $token = 'ENV3-' . bin2hex(random_bytes(16));
            $qrBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            
            DB::table('qrtoken')->insert([
                'id_asignacion' => $idAsignacion,
                'id_estado_qrtoken' => $estadoQrActivo->id,
                'token' => $token,
                'imagenqr' => $qrBase64,
                'fecha_creacion' => now()->subHours(5),
                'fecha_expiracion' => now()->addDays(7),
            ]);
        });

        // ========================================
        // Envío 4: PENDIENTE (SIN transportista ni vehículo)
        // ========================================
        DB::transaction(function () use ($clientesIds, $estadoPendiente, $estadoAsigPendiente, $tipoRefrigerado, $catalogoNaranjas) {
            // Ruta: Tarija → Cochabamba (ruta por carretera)
            $rutaCompleta = [
                [-64.7296, -21.5355],  // Tarija (inicio)
                [-64.7000, -21.4000],  // Salida de Tarija
                [-64.6000, -21.2000],  // Carretera hacia norte
                [-64.5000, -20.8000],  // Zona intermedia
                [-65.0000, -20.0000],  // Continuando
                [-65.5000, -19.0000],  // Acercándose a Potosí
                [-65.7550, -19.5836],  // Potosí (punto intermedio)
                [-66.0000, -19.0000],  // Continuando
                [-66.1500, -18.5000],  // Zona intermedia
                [-66.2789, -17.3936]   // Cochabamba (destino)
            ];
            
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => $clientesIds[3],
                'nombreorigen' => 'Finca Citrus del Valle, Valle de Tarija - Tarija',
                'origen_lng' => -64.7296,
                'origen_lat' => -21.5355,
                'nombredestino' => 'Centro de Distribución Andes, Sacaba - Cochabamba',
                'destino_lng' => -66.2789,
                'destino_lat' => -17.3936,
                'rutageojson' => json_encode([
                    'type' => 'LineString',
                    'coordinates' => $rutaCompleta
                ], JSON_UNESCAPED_SLASHES),
            ]);
            
            // Crear segmentos
            $segmentos = [
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 0, 7),
                        'properties' => ['tramo' => 'Tarija - Potosí', 'distancia_km' => 430]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 6),
                        'properties' => ['tramo' => 'Potosí - Cochabamba', 'distancia_km' => 360]
                    ], JSON_UNESCAPED_SLASHES)
                ]
            ];
            
            foreach ($segmentos as $segmento) {
                DB::table('direccionsegmento')->insert($segmento);
            }

            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[3],
                'fecha_creacion' => now()->subHours(2),
                'fecha_inicio' => null,
                'fecha_entrega' => null,
                'id_direccion' => $idDireccion,
            ]);

            // Solo estado Pendiente (no tiene asignación aún)
            DB::table('historialestados')->insert([
                'id_envio' => $idEnvio,
                'id_estado_envio' => $estadoPendiente->id,
                'fecha' => now()->subHours(2),
            ]);

            $idRecogida = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->addDays(2)->format('Y-m-d'),
                'hora_recogida' => '06:00:00',
                'hora_entrega' => '20:00:00',
                'instrucciones_recogida' => 'Contactar al encargado de almacén Sr. Martínez. Producto fresco.',
                'instrucciones_entrega' => 'Descargar en muelle de carga del centro de distribución.',
            ]);

            $idUnidadCAJA = DB::table('unidades_medida')->where('codigo', 'CAJA')->first()->id;
            $idCarga = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoNaranjas->id,
                'cantidad' => 100,
                'peso' => 1500.00,
                'id_unidad_medida' => $idUnidadCAJA,
            ]);

            // GENERAR CÓDIGO DE ACCESO (siempre se genera al crear el envío)
            $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

            // Asignación sin transportista ni vehículo (pendiente de asignar)
            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => null,  // Sin asignar
                'id_vehiculo' => null,  // Sin asignar
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoRefrigerado->id,
                'id_estado_asignacion' => $estadoAsigPendiente->id,
                'codigo_acceso' => $codigoAcceso,  // CÓDIGO DE ACCESO GENERADO AL CREAR EL ENVÍO
                'fecha_asignacion' => now()->subHours(2),  // Se registra cuando el cliente crea el envío
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ]);

            DB::table('asignacioncarga')->insert([
                'id_asignacion' => $idAsignacion,
                'id_carga' => $idCarga,
            ]);
        });

        // ========================================
        // Envío 5: ASIGNADO multitemperatura (múltiples productos)
        // ========================================
        DB::transaction(function () use ($clientesIds, $transportistasIds, $vehiculosIds, $estadoAsignado, $estadoAsigPendiente, $estadoTranspNoDisponible, $estadoVehNoDisponible, $tipoMultitemp, $catalogoFrutas, $catalogoTomates) {
            // Ruta: Trinidad (Beni) → La Paz (ruta larga por Rurrenabaque y Coroico)
            $rutaCompleta = [
                [-64.8996, -14.8336],  // Trinidad (inicio)
                [-65.0000, -14.7000],  // Salida de Trinidad
                [-65.4000, -14.5000],  // Hacia San Borja
                [-66.0000, -14.8000],  // San Borja
                [-66.5000, -14.5000],  // Hacia Rurrenabaque
                [-67.5274, -14.4429],  // Rurrenabaque (punto intermedio)
                [-67.7000, -15.0000],  // Subiendo los Yungas
                [-67.8000, -15.5000],  // Camino de montaña
                [-67.7313, -16.1886],  // Coroico (punto intermedio)
                [-67.9000, -16.3000],  // Bajada hacia El Alto
                [-68.1500, -16.5500],  // El Alto
                [-68.1193, -16.5000]   // La Paz (destino)
            ];
            
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => $clientesIds[4],
                'nombreorigen' => 'Productora Amazónica, Trinidad - Beni',
                'origen_lng' => -64.8996,
                'origen_lat' => -14.8336,
                'nombredestino' => 'Mercado Mayorista, Zona Villa Fátima - La Paz',
                'destino_lng' => -68.1193,
                'destino_lat' => -16.5000,
                'rutageojson' => json_encode([
                    'type' => 'LineString',
                    'coordinates' => $rutaCompleta
                ], JSON_UNESCAPED_SLASHES),
            ]);
            
            // Crear segmentos (ruta larga con puntos críticos)
            $segmentos = [
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 0, 6),
                        'properties' => ['tramo' => 'Trinidad - Rurrenabaque', 'distancia_km' => 380]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 5, 4),
                        'properties' => ['tramo' => 'Rurrenabaque - Coroico (Los Yungas)', 'distancia_km' => 250]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 8),
                        'properties' => ['tramo' => 'Coroico - La Paz', 'distancia_km' => 95]
                    ], JSON_UNESCAPED_SLASHES)
                ]
            ];
            
            foreach ($segmentos as $segmento) {
                DB::table('direccionsegmento')->insert($segmento);
            }

            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[4],
                'fecha_creacion' => now()->subDay(),
                'fecha_inicio' => null,
                'fecha_entrega' => null,
                'id_direccion' => $idDireccion,
            ]);

            DB::table('historialestados')->insert([
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'Pendiente')->first()->id, 'fecha' => now()->subDay()],
                ['id_envio' => $idEnvio, 'id_estado_envio' => $estadoAsignado->id, 'fecha' => now()->subHours(20)],
            ]);

            $idRecogida = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->addHours(12)->format('Y-m-d'),
                'hora_recogida' => '04:00:00',
                'hora_entrega' => '22:00:00',
                'instrucciones_recogida' => 'Transporte de larga distancia. Verificar combustible y condiciones del vehículo.',
                'instrucciones_entrega' => 'Llegada estimada entre 20:00-22:00. Descarga inmediata.',
            ]);

            // Múltiples cargas (carga mixta - multitemperatura)
            $idUnidadCAJA = DB::table('unidades_medida')->where('codigo', 'CAJA')->first()->id;
            $idCarga1 = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoFrutas->id,
                'cantidad' => 80,
                'peso' => 800.00,
                'id_unidad_medida' => $idUnidadCAJA,
            ]);
            
            $idCarga2 = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoTomates->id,
                'cantidad' => 120,
                'peso' => 960.00,
                'id_unidad_medida' => $idUnidadCAJA,
            ]);

            // GENERAR CÓDIGO DE ACCESO
            $codigoAcceso = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

            // Usar transportista 3 (que está disponible) y vehículo 5
            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => $transportistasIds[3],  // Usar transportista 4 (índice 3)
                'id_vehiculo' => $vehiculosIds[5],  // Usar vehículo 6 (índice 5)
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoMultitemp->id,
                'id_estado_asignacion' => $estadoAsigPendiente->id,
                'codigo_acceso' => $codigoAcceso,
                'fecha_asignacion' => now()->subHours(20),
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ]);
            
            // Marcar como NO DISPONIBLES
            DB::table('transportistas')->where('id', $transportistasIds[3])->update(['id_estado_transportista' => $estadoTranspNoDisponible->id]);
            DB::table('vehiculos')->where('id', $vehiculosIds[5])->update(['id_estado_vehiculo' => $estadoVehNoDisponible->id]);

            // Asociar ambas cargas
            DB::table('asignacioncarga')->insert([
                ['id_asignacion' => $idAsignacion, 'id_carga' => $idCarga1],
                ['id_asignacion' => $idAsignacion, 'id_carga' => $idCarga2],
            ]);

            // QR Token activo
            $estadoQrActivo = DB::table('estados_qrtoken')->where('nombre', 'Activo')->first();
            $token = 'ENV5-' . bin2hex(random_bytes(16));
            $qrBase64 = 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==';
            
            DB::table('qrtoken')->insert([
                'id_asignacion' => $idAsignacion,
                'id_estado_qrtoken' => $estadoQrActivo->id,
                'token' => $token,
                'imagenqr' => $qrBase64,
                'fecha_creacion' => now()->subHours(20),
                'fecha_expiracion' => now()->addDays(10),
            ]);
        });

        $this->command->info('  ✓ Envíos (5) creados con rutas geográficas realistas:');
        $this->command->info('    - Envío 1: ENTREGADO (Warnes→La Paz) - Transportista 1/Vehículo 1 DISPONIBLES');
        $this->command->info('    - Envío 2: EN CURSO (Challapata→La Paz) - Transportista 2/Vehículo 2 NO DISPONIBLES');
        $this->command->info('    - Envío 3: ASIGNADO (Cochabamba→La Paz) - Transportista 3/Vehículo 3 NO DISPONIBLES');
        $this->command->info('    - Envío 4: PENDIENTE (Tarija→Cochabamba) - Sin asignación de recursos');
        $this->command->info('    - Envío 5: ASIGNADO Multitemperatura (Trinidad→La Paz) - Transportista 4/Vehículo 6 NO DISPONIBLES');

        // ========================================
        // Envío 6: PENDIENTE con 2 PARTICIONES
        // ========================================
        $this->seedEnvioCon2Particiones($clientesIds, $estadoPendiente, $estadoAsigPendiente, $tipoRefrigerado, $tipoIsotermico, $catalogoFrutas, $catalogoVerduras);
    }

    private function seedEnvioCon2Particiones($clientesIds, $estadoPendiente, $estadoAsigPendiente, $tipoRefrigerado, $tipoIsotermico, $catalogoFrutas, $catalogoVerduras)
    {
        DB::transaction(function () use ($clientesIds, $estadoPendiente, $estadoAsigPendiente, $tipoRefrigerado, $tipoIsotermico, $catalogoFrutas, $catalogoVerduras) {
            // Ruta: Santa Cruz → Cochabamba
            $rutaCompleta = [
                [-63.1812, -17.7955],  // Santa Cruz (inicio)
                [-63.5000, -17.7000],  // Saliendo de Santa Cruz
                [-64.0000, -17.6000],  // Carretera hacia Cochabamba
                [-64.7300, -17.4500],  // Yapacaní
                [-65.2500, -17.4000],  // Zona intermedia
                [-65.7500, -17.3900],  // Llegando a Cochabamba
                [-66.1568, -17.3936]   // Cochabamba (destino)
            ];
            
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => $clientesIds[0],
                'nombreorigen' => 'Almacén Central, Santa Cruz de la Sierra',
                'origen_lng' => -63.1812,
                'origen_lat' => -17.7955,
                'nombredestino' => 'Centro de Distribución, Cochabamba',
                'destino_lng' => -66.1568,
                'destino_lat' => -17.3936,
                'rutageojson' => json_encode([
                    'type' => 'LineString',
                    'coordinates' => $rutaCompleta
                ], JSON_UNESCAPED_SLASHES),
            ]);
            
            // Crear segmentos
            $segmentos = [
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 0, 4),
                        'properties' => ['tramo' => 'Santa Cruz - Yapacaní', 'distancia_km' => 220]
                    ], JSON_UNESCAPED_SLASHES)
                ],
                [
                    'direccion_id' => $idDireccion,
                    'segmentogeojson' => json_encode([
                        'type' => 'LineString',
                        'coordinates' => array_slice($rutaCompleta, 3),
                        'properties' => ['tramo' => 'Yapacaní - Cochabamba', 'distancia_km' => 300]
                    ], JSON_UNESCAPED_SLASHES)
                ]
            ];
            
            foreach ($segmentos as $segmento) {
                DB::table('direccionsegmento')->insert($segmento);
            }

            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[0],
                'fecha_creacion' => now()->subHours(4),
                'fecha_inicio' => null,
                'fecha_entrega' => null,
                'id_direccion' => $idDireccion,
            ]);

            // Estado: Pendiente (sin asignaciones aún)
            DB::table('historialestados')->insert([
                'id_envio' => $idEnvio,
                'id_estado_envio' => $estadoPendiente->id,
                'fecha' => now()->subHours(4),
            ]);

            // ========== PARTICIÓN 1: Frutas (Refrigerado) ==========
            $idRecogida1 = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->addDays(1)->format('Y-m-d'),
                'hora_recogida' => '06:00:00',
                'hora_entrega' => '14:00:00',
                'instrucciones_recogida' => 'Partición 1: Recoger frutas frescas del almacén refrigerado.',
                'instrucciones_entrega' => 'Entregar en cámara fría del centro de distribución.',
            ]);

            $idUnidadCAJA = DB::table('unidades_medida')->where('codigo', 'CAJA')->first()->id;
            $idCarga1 = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoFrutas->id,
                'cantidad' => 120,
                'peso' => 1200.00,
                'id_unidad_medida' => $idUnidadCAJA,
            ]);

            // GENERAR CÓDIGO DE ACCESO para partición 1
            $codigoAcceso1 = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

            // Asignación 1 sin transportista (PENDIENTE)
            $idAsignacion1 = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => null,
                'id_vehiculo' => null,
                'id_recogida_entrega' => $idRecogida1,
                'id_tipo_transporte' => $tipoRefrigerado->id,
                'id_estado_asignacion' => $estadoAsigPendiente->id,
                'codigo_acceso' => $codigoAcceso1,  // CÓDIGO DE ACCESO GENERADO
                'fecha_asignacion' => now()->subHours(4),
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ]);

            DB::table('asignacioncarga')->insert([
                'id_asignacion' => $idAsignacion1,
                'id_carga' => $idCarga1,
            ]);

            // ========== PARTICIÓN 2: Verduras (Isotérmico) ==========
            $idRecogida2 = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->addDays(1)->format('Y-m-d'),
                'hora_recogida' => '06:30:00',
                'hora_entrega' => '14:30:00',
                'instrucciones_recogida' => 'Partición 2: Recoger verduras del almacén seco.',
                'instrucciones_entrega' => 'Entregar en zona de productos secos.',
            ]);

            $idCarga2 = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoVerduras->id,
                'cantidad' => 150,
                'peso' => 750.00,
                'id_unidad_medida' => $idUnidadCAJA,
            ]);

            // GENERAR CÓDIGO DE ACCESO para partición 2
            $codigoAcceso2 = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

            // Asignación 2 sin transportista (PENDIENTE)
            $idAsignacion2 = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => null,
                'id_vehiculo' => null,
                'id_recogida_entrega' => $idRecogida2,
                'id_tipo_transporte' => $tipoIsotermico->id,
                'id_estado_asignacion' => $estadoAsigPendiente->id,
                'codigo_acceso' => $codigoAcceso2,  // CÓDIGO DE ACCESO GENERADO
                'fecha_asignacion' => now()->subHours(4),
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ]);

            DB::table('asignacioncarga')->insert([
                'id_asignacion' => $idAsignacion2,
                'id_carga' => $idCarga2,
            ]);
        });

        $this->command->info('  ✓ Envío 6: PENDIENTE con 2 PARTICIONES (Santa Cruz→Cochabamba) - Sin transportistas asignados');
    }

    private function seedNotificaciones($clientesIds)
    {
        if (empty($clientesIds)) {
            $this->command->warn('  ⚠ No hay clientes para crear notificaciones');
            return;
        }

        // Obtener envíos para vincular notificaciones
        $envios = DB::table('envios')->whereIn('id_usuario', $clientesIds)->limit(3)->get();

        $notificaciones = [
            [
                'id_usuario' => $clientesIds[0],
                'tipo' => 'estado_envio',
                'titulo' => 'Envío entregado',
                'mensaje' => 'Tu envío de manzanas ha sido entregado exitosamente',
                'leida' => true,
                'id_envio' => $envios[0]->id ?? null,
                'fecha' => now()->subDays(7),
            ],
            [
                'id_usuario' => $clientesIds[0],
                'tipo' => 'estado_envio',
                'titulo' => 'Envío en curso',
                'mensaje' => 'Tu envío está en camino hacia La Paz',
                'leida' => true,
                'id_envio' => $envios[0]->id ?? null,
                'fecha' => now()->subDays(9),
            ],
            [
                'id_usuario' => $clientesIds[1],
                'tipo' => 'estado_envio',
                'titulo' => 'Envío recogido',
                'mensaje' => 'El transportista ha recogido tu envío de quinua',
                'leida' => false,
                'id_envio' => $envios[1]->id ?? null,
                'fecha' => now()->subDays(2),
            ],
            [
                'id_usuario' => $clientesIds[1],
                'tipo' => 'asignacion',
                'titulo' => 'Transportista asignado',
                'mensaje' => 'Roberto Vargas ha sido asignado a tu envío',
                'leida' => false,
                'id_envio' => $envios[1]->id ?? null,
                'fecha' => now()->subDays(3),
            ],
            [
                'id_usuario' => $clientesIds[2],
                'tipo' => 'estado_envio',
                'titulo' => 'Envío creado',
                'mensaje' => 'Tu envío de lechugas ha sido registrado exitosamente',
                'leida' => false,
                'id_envio' => $envios[2]->id ?? null,
                'fecha' => now()->subHours(6),
            ],
        ];

        foreach ($notificaciones as $notif) {
            if ($notif['id_envio']) {
                DB::table('notificaciones')->insert($notif);
            }
        }

        $this->command->info('  ✓ Notificaciones (5) - 2 no leídas');
    }

    private function seedCalificaciones($clientesIds, $transportistasIds)
    {
        if (empty($clientesIds) || empty($transportistasIds)) {
            $this->command->warn('  ⚠ No hay suficientes datos para crear calificaciones');
            return;
        }

        // Solo calificar el envío completado (el primero)
        $envioCompletado = DB::table('envios')
            ->where('id_usuario', $clientesIds[0])
            ->whereNotNull('fecha_entrega')
            ->first();

        if (!$envioCompletado) {
            $this->command->warn('  ⚠ No hay envíos completados para calificar');
            return;
        }

        $calificaciones = [
            [
                'id_envio' => $envioCompletado->id,
                'id_usuario' => $clientesIds[0],
                'id_transportista' => $transportistasIds[0],
                'puntuacion' => 5,
                'comentario' => 'Excelente servicio, el producto llegó en perfecto estado y a tiempo.',
                'fecha' => now()->subDays(7),
            ],
            [
                'id_envio' => $envioCompletado->id,
                'id_usuario' => $clientesIds[1],
                'id_transportista' => $transportistasIds[1],
                'puntuacion' => 4,
                'comentario' => 'Buen servicio, pero hubo un pequeño retraso.',
                'fecha' => now()->subDays(5),
            ],
        ];

        foreach ($calificaciones as $calif) {
            DB::table('calificaciones')->insert($calif);
        }

        $this->command->info('  ✓ Calificaciones (2) - Promedio: 4.5 estrellas');
    }
}
