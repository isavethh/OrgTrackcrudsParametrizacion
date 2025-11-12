<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Usuario;
use App\Models\Admin;
use App\Models\Cliente;
use App\Models\Vehiculo;
use App\Models\Transportista;
use App\Models\TipoTransporte;
use App\Models\TamanoTransporte;
use App\Models\TipoEmpaque;
use App\Models\EstadoTransportista;
use App\Models\Direccion;
use App\Models\Envio;

class DatosIniciales extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear Usuarios Base y Extensiones
        
        // Admin
        $adminUser = Usuario::create([
            'nombre' => 'Admin',
            'apellido' => 'Sistema',
            'correo' => 'admin@orgtrack.com',
            'contrasena' => Hash::make('admin123'),
        ]);
        
        $admin = Admin::create([
            'usuario_id' => $adminUser->id,
            'nivel_acceso' => 3,
        ]);

        // Cliente 1
        $cliente1User = Usuario::create([
            'nombre' => 'Juan',
            'apellido' => 'Pérez',
            'correo' => 'juan.perez@gmail.com',
            'contrasena' => Hash::make('cliente123'),
        ]);
        
        Cliente::create([
            'usuario_id' => $cliente1User->id,
            'telefono' => '71234567',
            'direccion_entrega' => 'Av. 6 de Agosto #1234, La Paz',
        ]);

        // Cliente 2
        $cliente2User = Usuario::create([
            'nombre' => 'María',
            'apellido' => 'López',
            'correo' => 'maria.lopez@gmail.com',
            'contrasena' => Hash::make('cliente123'),
        ]);
        
        Cliente::create([
            'usuario_id' => $cliente2User->id,
            'telefono' => '72345678',
            'direccion_entrega' => 'Zona Sur, Calacoto, La Paz',
        ]);

        // Transportista 1
        $transportista1User = Usuario::create([
            'nombre' => 'Carlos',
            'apellido' => 'Rodríguez',
            'correo' => 'carlos.rodriguez@gmail.com',
            'contrasena' => Hash::make('trans123'),
        ]);
        
        $estadoDisponible = EstadoTransportista::where('nombre', 'Disponible')->first();
        Transportista::create([
            'usuario_id' => $transportista1User->id,
            'ci' => '12345678',
            'placa' => 'ABC-1234',
            'telefono' => '77123456',
            'estado_id' => $estadoDisponible->id,
        ]);

        // Transportista 2
        $transportista2User = Usuario::create([
            'nombre' => 'Luis',
            'apellido' => 'Martínez',
            'correo' => 'luis.martinez@gmail.com',
            'contrasena' => Hash::make('trans123'),
        ]);
        
        $estadoEnRuta = EstadoTransportista::where('nombre', 'En ruta')->first();
        Transportista::create([
            'usuario_id' => $transportista2User->id,
            'ci' => '87654321',
            'placa' => 'XYZ-5678',
            'telefono' => '78987654',
            'estado_id' => $estadoEnRuta->id,
        ]);

        // Crear catálogos si no existen
        
        // Tipos de Transporte (además de los que ya existen en schema.sql)
        TipoTransporte::firstOrCreate(['nombre' => 'Camión']);
        TipoTransporte::firstOrCreate(['nombre' => 'Camioneta']);
        TipoTransporte::firstOrCreate(['nombre' => 'Van']);

        // Tamaños de Transporte
        TamanoTransporte::firstOrCreate(['nombre' => 'Pequeño']);
        TamanoTransporte::firstOrCreate(['nombre' => 'Mediano']);
        TamanoTransporte::firstOrCreate(['nombre' => 'Grande']);

        // Tipos de Empaque
        TipoEmpaque::firstOrCreate(['nombre' => 'Caja de Cartón']);
        TipoEmpaque::firstOrCreate(['nombre' => 'Pallet']);
        TipoEmpaque::firstOrCreate(['nombre' => 'Contenedor']);
        TipoEmpaque::firstOrCreate(['nombre' => 'Bolsa']);

        // Crear Vehículos
        $tipoCamion = TipoTransporte::where('nombre', 'Camión')->first();
        $tamanoGrande = TamanoTransporte::where('nombre', 'Grande')->first();
        $tamanoMediano = TamanoTransporte::where('nombre', 'Mediano')->first();
        
        Vehiculo::create([
            'admin_id' => $admin->id,
            'tipo_transporte_id' => $tipoCamion->id,
            'tamano_transporte_id' => $tamanoGrande->id,
            'placa' => 'CAM-123',
            'marca' => 'Volvo',
            'modelo' => 'FH16',
            'estado' => 'Disponible',
        ]);

        Vehiculo::create([
            'admin_id' => $admin->id,
            'tipo_transporte_id' => $tipoCamion->id,
            'tamano_transporte_id' => $tamanoMediano->id,
            'placa' => 'CAM-456',
            'marca' => 'Mercedes',
            'modelo' => 'Actros',
            'estado' => 'En ruta',
        ]);

        // Crear Envíos
        $tipoEmpaqueCaja = TipoEmpaque::where('nombre', 'Caja de Cartón')->first();
        $unidadKg = \App\Models\UnidadMedida::where('nombre', 'kg')->first();

        $envio1 = Envio::create([
            'admin_id' => $admin->id,
            'tipo_empaque_id' => $tipoEmpaqueCaja->id,
            'unidad_medida_id' => $unidadKg->id,
            'estado' => 'Pendiente',
            'volumen' => 5.5,
            'peso' => 250.50,
            'fecha_envio' => now(),
            'fecha_entrega_estimada' => now()->addDays(3),
        ]);

        $envio2 = Envio::create([
            'admin_id' => $admin->id,
            'tipo_empaque_id' => $tipoEmpaqueCaja->id,
            'unidad_medida_id' => $unidadKg->id,
            'estado' => 'En curso',
            'volumen' => 3.2,
            'peso' => 150.00,
            'fecha_envio' => now()->subDay(),
            'fecha_entrega_estimada' => now()->addDays(2),
        ]);

        $envio3 = Envio::create([
            'admin_id' => $admin->id,
            'tipo_empaque_id' => $tipoEmpaqueCaja->id,
            'unidad_medida_id' => $unidadKg->id,
            'estado' => 'Entregado',
            'volumen' => 2.8,
            'peso' => 120.75,
            'fecha_envio' => now()->subDays(5),
            'fecha_entrega_estimada' => now()->subDays(2),
        ]);

        // Crear Direcciones (ahora pertenecen a envíos)
        Direccion::create([
            'envio_id' => $envio1->id,
            'nombre_ruta' => 'La Paz - El Alto',
            'descripcion' => 'Ruta desde Av. 6 de Agosto hasta Calle Comercio',
            'latitud' => -16.5000,
            'longitud' => -68.1500,
            'orden' => 1,
        ]);

        Direccion::create([
            'envio_id' => $envio1->id,
            'nombre_ruta' => 'El Alto - Destino Final',
            'descripcion' => 'Punto de entrega final',
            'latitud' => -16.5050,
            'longitud' => -68.1600,
            'orden' => 2,
        ]);

        Direccion::create([
            'envio_id' => $envio2->id,
            'nombre_ruta' => 'Calacoto - Centro La Paz',
            'descripcion' => 'Zona Sur hacia Centro',
            'latitud' => -16.5300,
            'longitud' => -68.0800,
            'orden' => 1,
        ]);

        $this->command->info('✅ Datos iniciales creados exitosamente!');
        $this->command->info('');
        $this->command->info('📧 USUARIOS CREADOS:');
        $this->command->info('   👨‍💼 Admin: admin@orgtrack.com / admin123');
        $this->command->info('   👤 Cliente 1: juan.perez@gmail.com / cliente123');
        $this->command->info('   👤 Cliente 2: maria.lopez@gmail.com / cliente123');
        $this->command->info('   🚚 Transportista 1: carlos.rodriguez@gmail.com / trans123');
        $this->command->info('   🚚 Transportista 2: luis.martinez@gmail.com / trans123');
        $this->command->info('');
        $this->command->info('📊 DATOS CREADOS:');
        $this->command->info('   - ' . Usuario::count() . ' usuarios base');
        $this->command->info('   - ' . Admin::count() . ' administradores');
        $this->command->info('   - ' . Cliente::count() . ' clientes');
        $this->command->info('   - ' . Transportista::count() . ' transportistas');
        $this->command->info('   - ' . Vehiculo::count() . ' vehículos');
        $this->command->info('   - ' . Envio::count() . ' envíos');
        $this->command->info('   - ' . Direccion::count() . ' direcciones/rutas');
    }
}
