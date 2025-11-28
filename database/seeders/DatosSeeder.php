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
            ['nombre' => 'Terrestre Refrigerado', 'descripcion' => 'Transporte con cadena de frío para productos frescos'],
            ['nombre' => 'Terrestre Estándar', 'descripcion' => 'Transporte sin refrigeración para granos y productos secos'],
            ['nombre' => 'Express', 'descripcion' => 'Transporte urgente para entregas prioritarias'],
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
            $existe = DB::table('usuarios')->where('correo', $clienteData['correo'])->exists();
            if ($existe) continue;

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
        ];

        $transportistasIds = [];

        foreach ($transportistas as $transportistaData) {
            // Verificar si ya existe
            $existe = DB::table('usuarios')->where('correo', $transportistaData['correo'])->exists();
            if ($existe) continue;

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

        $this->command->info('  ✓ Transportistas (3)');
        return $transportistasIds;
    }

    private function seedVehiculos()
    {
        $tipoCamion = DB::table('tipos_vehiculo')->where('nombre', 'Camión')->first();
        $tipoCamioneta = DB::table('tipos_vehiculo')->where('nombre', 'Camioneta')->first();
        $tipoFurgoneta = DB::table('tipos_vehiculo')->where('nombre', 'Furgoneta')->first();
        $estadoDisponible = DB::table('estados_vehiculo')->where('nombre', 'Disponible')->first();
        $tipoRefrigerado = DB::table('tipotransporte')->where('nombre', 'Terrestre Refrigerado')->first();
        $tipoEstandar = DB::table('tipotransporte')->where('nombre', 'Terrestre Estándar')->first();

        $vehiculos = [
            ['id_tipo_vehiculo' => $tipoCamion->id, 'placa' => 'SCZ-1234', 'capacidad' => 5000.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
            ['id_tipo_vehiculo' => $tipoCamion->id, 'placa' => 'LPZ-5678', 'capacidad' => 4500.00, 'id_tipo_transporte' => $tipoEstandar->id],
            ['id_tipo_vehiculo' => $tipoCamioneta->id, 'placa' => 'CBB-9012', 'capacidad' => 2000.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
            ['id_tipo_vehiculo' => $tipoCamioneta->id, 'placa' => 'TJA-3456', 'capacidad' => 1800.00, 'id_tipo_transporte' => $tipoEstandar->id],
            ['id_tipo_vehiculo' => $tipoFurgoneta->id, 'placa' => 'ORU-7890', 'capacidad' => 1000.00, 'id_tipo_transporte' => $tipoRefrigerado->id],
        ];

        $vehiculosIds = [];

        foreach ($vehiculos as $vehiculo) {
            // Verificar si ya existe
            $existe = DB::table('vehiculos')->where('placa', $vehiculo['placa'])->exists();
            if ($existe) continue;

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

        $this->command->info('  ✓ Vehículos (5) - Todos Disponibles');
        return $vehiculosIds;
    }

    private function seedEnvios($clientesIds, $transportistasIds, $vehiculosIds)
    {
        if (empty($clientesIds) || empty($transportistasIds) || empty($vehiculosIds)) {
            $this->command->warn('  ⚠ No hay suficientes datos para crear envíos');
            return;
        }

        $estadoPendiente = DB::table('estados_envio')->where('nombre', 'Pendiente')->first();
        $estadoEnTransito = DB::table('estados_envio')->where('nombre', 'En tránsito')->first();
        $estadoEntregado = DB::table('estados_envio')->where('nombre', 'Entregado')->first();
        
        $estadoAsigPendiente = DB::table('estados_asignacion_multiple')->where('nombre', 'Pendiente')->first();
        $estadoAsigEnCurso = DB::table('estados_asignacion_multiple')->where('nombre', 'En curso')->first();
        $estadoAsigCompletada = DB::table('estados_asignacion_multiple')->where('nombre', 'Completada')->first();
        
        $tipoRefrigerado = DB::table('tipotransporte')->where('nombre', 'Terrestre Refrigerado')->first();
        $tipoEstandar = DB::table('tipotransporte')->where('nombre', 'Terrestre Estándar')->first();
        
        $catalogoFrutas = DB::table('catalogo_carga')->where('tipo', 'Frutas')->where('variedad', 'Manzanas')->first();
        $catalogoVerduras = DB::table('catalogo_carga')->where('tipo', 'Verduras')->where('variedad', 'Lechugas')->first();
        $catalogoGranos = DB::table('catalogo_carga')->where('tipo', 'Granos')->where('variedad', 'Quinua')->first();
        
        $condiciones = DB::table('condiciones_transporte')->limit(5)->get();

        // Envío 1: Completado con documentos
        DB::transaction(function () use ($clientesIds, $transportistasIds, $vehiculosIds, $estadoPendiente, $estadoEnTransito, $estadoEntregado, $estadoAsigCompletada, $tipoRefrigerado, $catalogoFrutas, $condiciones) {
            // 1. Dirección: Productor Santa Cruz → Planta La Paz (sin id_usuario, como lo hace el admin)
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => null, // Las direcciones no se vinculan a usuarios
                'nombreorigen' => 'Finca El Paraíso, Warnes - Santa Cruz',
                'origen_lng' => -63.1812,
                'origen_lat' => -17.7833,
                'nombredestino' => 'Planta Procesadora OrganiCo, Zona Sur - La Paz',
                'destino_lng' => -68.1193,
                'destino_lat' => -16.5000,
                'rutageojson' => '{"type":"LineString","coordinates":[[-63.1812,-17.7833],[-68.1193,-16.5000]]}',
            ]);

            // 2. Envío completado
            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[0],
                'fecha_creacion' => now()->subDays(10),
                'fecha_inicio' => now()->subDays(9),
                'fecha_entrega' => now()->subDays(7),
                'id_direccion' => $idDireccion,
            ]);

            // 3. Historial: Pendiente → En tránsito → Entregado
            DB::table('historialestados')->insert([
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'Pendiente')->first()->id, 'fecha' => now()->subDays(10)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'En tránsito')->first()->id, 'fecha' => now()->subDays(9)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'Entregado')->first()->id, 'fecha' => now()->subDays(7)],
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
            $idCarga = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoFrutas->id,
                'cantidad' => 150,
                'peso' => 1500.00,
            ]);

            // 6. Asignación completada
            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => $transportistasIds[0],
                'id_vehiculo' => $vehiculosIds[0],
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoRefrigerado->id,
                'id_estado_asignacion' => DB::table('estados_asignacion_multiple')->where('nombre', 'Completada')->first()->id,
                'fecha_asignacion' => now()->subDays(10),
                'fecha_inicio' => now()->subDays(9),
                'fecha_fin' => now()->subDays(7),
            ]);

            // 7. Asociar carga
            DB::table('asignacioncarga')->insert([
                'id_asignacion' => $idAsignacion,
                'id_carga' => $idCarga,
            ]);

            // 8. Checklist completado
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

            // 9. Firmas (base64 simplificado para prueba)
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

            // 10. QR Token (usado)
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
        });

        // Envío 2: En tránsito
        DB::transaction(function () use ($clientesIds, $transportistasIds, $vehiculosIds, $estadoEnTransito, $estadoAsigEnCurso, $tipoEstandar, $catalogoGranos) {
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => null, // Sin vincular a usuario específico
                'nombreorigen' => 'Cooperativa Andina, Challapata - Oruro',
                'origen_lng' => -66.7667,
                'origen_lat' => -18.9167,
                'nombredestino' => 'Planta Procesadora OrganiCo, Zona Sur - La Paz',
                'destino_lng' => -68.1193,
                'destino_lat' => -16.5000,
                'rutageojson' => '{"type":"LineString","coordinates":[[-66.7667,-18.9167],[-68.1193,-16.5000]]}',
            ]);

            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[1],
                'fecha_creacion' => now()->subDays(3),
                'fecha_inicio' => now()->subDays(2),
                'fecha_entrega' => null,
                'id_direccion' => $idDireccion,
            ]);

            DB::table('historialestados')->insert([
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'Pendiente')->first()->id, 'fecha' => now()->subDays(3)],
                ['id_envio' => $idEnvio, 'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'En tránsito')->first()->id, 'fecha' => now()->subDays(2)],
            ]);

            $idRecogida = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->subDays(2)->format('Y-m-d'),
                'hora_recogida' => '07:00:00',
                'hora_entrega' => '18:00:00',
                'instrucciones_recogida' => 'Recoger en almacén principal de la cooperativa.',
                'instrucciones_entrega' => 'Descargar en almacén de granos secos.',
            ]);

            $idCarga = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoGranos->id,
                'cantidad' => 80,
                'peso' => 2000.00,
            ]);

            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => $transportistasIds[1],
                'id_vehiculo' => $vehiculosIds[1],
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoEstandar->id,
                'id_estado_asignacion' => DB::table('estados_asignacion_multiple')->where('nombre', 'En curso')->first()->id,
                'fecha_asignacion' => now()->subDays(3),
                'fecha_inicio' => now()->subDays(2),
                'fecha_fin' => null,
            ]);

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

            // QR Token (activo, aún en tránsito)
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

        // Envío 3: Pendiente
        DB::transaction(function () use ($clientesIds, $transportistasIds, $vehiculosIds, $estadoPendiente, $estadoAsigPendiente, $tipoRefrigerado, $catalogoVerduras) {
            $idDireccion = DB::table('direccion')->insertGetId([
                'id_usuario' => null, // Sin vincular a usuario específico
                'nombreorigen' => 'Cultivos Hidropónicos Verde Vida, Quillacollo - Cochabamba',
                'origen_lng' => -66.2789,
                'origen_lat' => -17.3936,
                'nombredestino' => 'Planta Procesadora OrganiCo, Zona Sur - La Paz',
                'destino_lng' => -68.1193,
                'destino_lat' => -16.5000,
                'rutageojson' => '{"type":"LineString","coordinates":[[-66.2789,-17.3936],[-68.1193,-16.5000]]}',
            ]);

            $idEnvio = DB::table('envios')->insertGetId([
                'id_usuario' => $clientesIds[2],
                'fecha_creacion' => now()->subHours(6),
                'fecha_inicio' => null,
                'fecha_entrega' => null,
                'id_direccion' => $idDireccion,
            ]);

            DB::table('historialestados')->insert([
                'id_envio' => $idEnvio,
                'id_estado_envio' => DB::table('estados_envio')->where('nombre', 'Pendiente')->first()->id,
                'fecha' => now()->subHours(6),
            ]);

            $idRecogida = DB::table('recogidaentrega')->insertGetId([
                'fecha_recogida' => now()->addDay()->format('Y-m-d'),
                'hora_recogida' => '05:00:00',
                'hora_entrega' => '14:00:00',
                'instrucciones_recogida' => 'Producto muy frágil. Mantener temperatura 2-4°C.',
                'instrucciones_entrega' => 'Entrega urgente en cámara refrigerada.',
            ]);

            $idCarga = DB::table('carga')->insertGetId([
                'id_catalogo_carga' => $catalogoVerduras->id,
                'cantidad' => 200,
                'peso' => 1000.00,
            ]);

            $idAsignacion = DB::table('asignacionmultiple')->insertGetId([
                'id_envio' => $idEnvio,
                'id_transportista' => $transportistasIds[2],
                'id_vehiculo' => $vehiculosIds[2],
                'id_recogida_entrega' => $idRecogida,
                'id_tipo_transporte' => $tipoRefrigerado->id,
                'id_estado_asignacion' => DB::table('estados_asignacion_multiple')->where('nombre', 'Pendiente')->first()->id,
                'fecha_asignacion' => now()->subHours(5),
                'fecha_inicio' => null,
                'fecha_fin' => null,
            ]);

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

        $this->command->info('  ✓ Envíos (3) con documentos completos:');
        $this->command->info('    - Envío 1: ENTREGADO con checklist y firmas');
        $this->command->info('    - Envío 2: EN TRÁNSITO con firma de transportista');
        $this->command->info('    - Envío 3: PENDIENTE programado');
    }
}
