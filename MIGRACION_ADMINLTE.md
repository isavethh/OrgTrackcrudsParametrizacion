# Guía de Migración a AdminLTE - OrgTrack

## ✅ Lo que ya está hecho:

1. **Layouts base creados:**
   - `resources/views/layouts/cliente.blade.php` - Para vistas de cliente
   - `resources/views/layouts/adminlte.blade.php` - Para vistas de admin

2. **Menús configurados:**
   - `config/menu-cliente.php` - Menú del cliente
   - `config/menu-admin.php` - Menú del administrador

3. **Dashboards migrados:**
   - ✅ `resources/views/cliente/dashboard.blade.php`
   - ✅ `resources/views/admin/dashboard.blade.php`

4. **Autenticación usando AdminLTE:**
   - ✅ `resources/views/auth/login.blade.php`
   - ✅ `resources/views/auth/register.blade.php`

## 📝 Cómo migrar las vistas restantes:

### Patrón de migración (3 pasos simples):

#### ANTES (vista antigua):
```php
@extends('cliente.layouts.app')

@section('title', 'Envíos - OrgTrack')
@section('page-title', 'Mis Envíos')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Dashboard</a></li>
    <li class="breadcrumb-item active">Envíos</li>
@endsection

@section('content')
    <!-- Tu contenido aquí -->
@endsection

@section('scripts')
    <!-- Tu JavaScript aquí -->
@endsection
```

#### DESPUÉS (vista migrada):
```php
@extends('layouts.cliente')

@section('page-title', 'Mis Envíos')

@section('page-content')
    <!-- Tu contenido aquí (sin cambios) -->
@endsection

@push('js')
    <script>
        <!-- Tu JavaScript aquí (sin cambios) -->
    </script>
@endpush
```

### Cambios necesarios por vista:

| Vista antigua | Nueva vista | Cambios |
|--------------|-------------|---------|
| `@extends('cliente.layouts.app')` | `@extends('layouts.cliente')` | Cambiar extends |
| `@extends('layouts.admin')` | `@extends('layouts.adminlte')` | Cambiar extends |
| `@section('content')` | `@section('page-content')` | Renombrar sección |
| `@section('scripts')` | `@push('js') <script>...</script> @endpush` | Usar push |
| `@section('breadcrumb')` | _Eliminar (AdminLTE lo maneja)_ | No necesario |

## 📂 Vistas pendientes de migrar:

### Cliente (15 vistas):
- [ ] `cliente/envios/index.blade.php`
- [ ] `cliente/envios/create.blade.php`
- [ ] `cliente/envios/show.blade.php`
- [ ] `cliente/direcciones/index.blade.php`
- [ ] `cliente/direcciones/create.blade.php`
- [ ] `cliente/documentos/index.blade.php`
- [ ] `cliente/documentos/create.blade.php`
- [ ] `cliente/documentos/ver.blade.php`
- [ ] `cliente/documentos/particiones.blade.php`
- [ ] `cliente/transportistas/index.blade.php`
- [ ] `cliente/vehiculos/index.blade.php`
- [ ] `cliente/welcome.blade.php`

### Admin (19 vistas):
- [ ] `admin/envios/index.blade.php`
- [ ] `admin/envios/create.blade.php`
- [ ] `admin/envios/show.blade.php`
- [ ] `admin/direcciones/index.blade.php`
- [ ] `admin/direcciones/create.blade.php`
- [ ] `admin/documentos/index.blade.php`
- [ ] `admin/documentos/create.blade.php`
- [ ] `admin/documentos/ver.blade.php`
- [ ] `admin/documentos/particiones.blade.php`
- [ ] `admin/documentos/cliente.blade.php`
- [ ] `admin/transportistas/index.blade.php`
- [ ] `admin/vehiculos/index.blade.php`
- [ ] `admin/usuarios/index.blade.php`
- [ ] `admin/condiciones/index.blade.php`
- [ ] `admin/incidentes/index.blade.php`
- [ ] `admin/unidades_medida/index.blade.php`
- [ ] `admin/catalogo_carga/index.blade.php`
- [ ] `admin/checklists/catalogos.blade.php`

## 🚀 Ventajas de la migración:

1. **Código más limpio:** Las vistas son más simples y fáciles de mantener
2. **Menús dinámicos:** Los menús se configuran una vez en `config/menu-*.php`
3. **Consistencia visual:** Todo usa el mismo sistema de AdminLTE
4. **Mejor performance:** AdminLTE optimiza la carga de recursos
5. **Responsive por defecto:** Funciona en móviles automáticamente
6. **Actualizaciones fáciles:** Actualizar AdminLTE actualiza todo

## 📌 Notas importantes:

1. **No se pierde funcionalidad:** Todo tu JavaScript y lógica se mantiene igual
2. **Los formularios funcionan igual:** No hay cambios en forms, inputs, validaciones
3. **Las rutas no cambian:** Todas tus rutas siguen funcionando
4. **Compatibilidad total:** Se puede migrar vista por vista sin romper nada

## 🔧 Configuración AdminLTE:

Para personalizar más AdminLTE, edita `config/adminlte.php`:
- Cambiar logo
- Personalizar colores
- Habilitar plugins (DataTables, Select2, etc.)
- Modificar comportamiento del sidebar
- Agregar items al navbar

## ¿Quieres que migre automáticamente todas las vistas?

Solo dime "sí" y lo hago. El proceso es seguro y reversible.
