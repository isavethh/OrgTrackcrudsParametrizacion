# Cambios Implementados - Sistema de Aprobación de Envíos

## 🎯 Resumen de Cambios

Se ha implementado un nuevo flujo de trabajo para los envíos donde:
1. **AgroNexus** crea los envíos (incluyendo tipo de vehículo)
2. **OrgTrack** los recibe, aprueba/rechaza y asigna transportistas
3. La **API** gestiona la sincronización entre ambos sistemas

---

## ✅ Cambios Realizados

### 1. **Arreglar Fechas en OrgTrack** ✓

**Archivo**: `OrgTrackcrudsParametrizacion/resources/views/envios/show.blade.php`

- Agregada **Fecha Entrega Estimada** con hora
- Separada **Fecha de Entrega Real** de la estimada
- Mejor formato y visualización de fechas

---

### 2. **Endpoint de Tipos de Vehículo** ✓

#### API Logística

**Nuevos archivos**:
- `Api_Logistica/app/Models/TipoVehiculo.php`
- `Api_Logistica/app/Http/Controllers/Api/TipoVehiculoController.php`

**Endpoints**:
- `GET /api/tipos-vehiculo` - Lista todos los tipos de vehículo
- `GET /api/tipos-vehiculo/{id}` - Obtiene un tipo específico

**Rutas actualizadas**: `Api_Logistica/routes/api.php`

---

### 3. **Campos Nuevos en Base de Datos** ✓

#### Nueva Migración

**Archivo**: `Api_Logistica/database/migrations/2025_11_30_000001_add_vehiculo_and_transportista_to_envios.php`

**Campos agregados a tabla `envios`**:
- `id_tipo_vehiculo` (nullable) - FK a tipos_vehiculo
- `id_transportista_asignado` (nullable) - FK a usuarios
- `estado_aprobacion` (default: 'pendiente') - pendiente|aprobado|rechazado
- `motivo_rechazo` (nullable) - Texto con motivo si es rechazado

**⚠️ IMPORTANTE**: Ejecutar esta migración en la API:
```bash
cd "Api_Logistica"
php artisan migrate
```

---

### 4. **AgroNexus - Selector de Tipo de Vehículo** ✓

**Archivos modificados**:
- `AgroNexus/app/Http/Controllers/Web/EnvioController.php`
  - Método `create()`: Obtiene tipos de vehículo desde API
  - Método `edit()`: Obtiene tipos de vehículo desde API  
  - Método `store()`: Envía tipo de vehículo a la API
- `AgroNexus/resources/views/envios/create.blade.php`
  - Nuevo campo select para tipo de vehículo

**Funcionalidad**:
- El usuario de AgroNexus puede seleccionar el tipo de vehículo al crear un envío
- El campo es opcional
- Los tipos vienen directamente de OrgTrack a través de la API

---

### 5. **OrgTrack - Quitar Crear Envíos** ✓

**Archivos modificados**:
- `OrgTrackcrudsParametrizacion/resources/views/envios/index.blade.php`
  - Removido botón "Nuevo Envío"
  - Agregado mensaje informativo: "Los envíos llegan desde AgroNexus"

**Justificación**: OrgTrack solo debe recibir y gestionar envíos, no crearlos.

---

### 6. **OrgTrack - Sistema de Aprobación** ✓

#### Vista Index

**Archivo**: `OrgTrackcrudsParametrizacion/resources/views/envios/index.blade.php`

**Cambios**:
- Columna "Estado" cambiada a "Estado Aprobación"
- Badges de colores según estado:
  - 🟡 **Amarillo**: Pendiente Aprobación
  - 🟢 **Verde**: Aprobado
  - 🔴 **Rojo**: Rechazado
- Botones de acción:
  - ✅ **Aprobar**: Solo visible si está pendiente
  - ❌ **Rechazar**: Solo visible si está pendiente
  - 👁️ **Ver**: Siempre visible

#### Modales

**Modal de Aprobación**:
- Selector de transportista (solo usuarios con rol "Transportista")
- Al aprobar, asigna automáticamente el transportista

**Modal de Rechazo**:
- Campo de texto para motivo (mínimo 10 caracteres)
- Advertencia de que la acción no se puede deshacer

#### Vista Show

**Archivo**: `OrgTrackcrudsParametrizacion/resources/views/envios/show.blade.php`

**Información adicional mostrada**:
- Estado de Aprobación (badge grande)
- Motivo de Rechazo (si aplica, en alerta roja)
- Tipo de Vehículo (si fue seleccionado)
- Transportista Asignado (si fue aprobado)

---

### 7. **Controlador de OrgTrack** ✓

**Archivo**: `OrgTrackcrudsParametrizacion/app/Http/Controllers/EnvioController.php`

**Nuevos métodos**:

```php
public function aprobar(Request $request, Envio $envio)
```
- Valida que se haya seleccionado un transportista
- Actualiza estado a 'aprobado' en BD local
- Asigna el transportista
- Sincroniza con la API
- Retorna JSON para AJAX

```php
public function rechazar(Request $request, Envio $envio)
```
- Valida motivo (mín. 10 caracteres)
- Actualiza estado a 'rechazado' en BD local
- Guarda motivo del rechazo
- Sincroniza con la API
- Retorna JSON para AJAX

**Rutas agregadas**: `OrgTrackcrudsParametrizacion/routes/web.php`
```php
Route::post('envios/{envio}/aprobar', [EnvioController::class, 'aprobar']);
Route::post('envios/{envio}/rechazar', [EnvioController::class, 'rechazar']);
```

---

### 8. **API - Métodos de Aprobación** ✓

**Archivo**: `Api_Logistica/app/Http/Controllers/Api/EnvioController.php`

**Nuevos métodos**:

```php
public function aprobar(Request $request, Envio $envio)
```
- Endpoint: `POST /api/envios/{id}/aprobar`
- Parámetros: `id_transportista_asignado` (required)
- Actualiza estado y asigna transportista

```php
public function rechazar(Request $request, Envio $envio)
```
- Endpoint: `POST /api/envios/{id}/rechazar`
- Parámetros: `motivo_rechazo` (required, min:10)
- Actualiza estado y guarda motivo

**Rutas agregadas**: `Api_Logistica/routes/api.php`

---

### 9. **Modelos Actualizados** ✓

#### API Logística

**Archivo**: `Api_Logistica/app/Models/Envio.php`

**Campos agregados a $fillable**:
- `id_tipo_vehiculo`
- `id_transportista_asignado`
- `estado_aprobacion`
- `motivo_rechazo`

**Nuevas relaciones**:
```php
public function tipoVehiculo()
public function transportistaAsignado()
```

#### OrgTrack

**Archivo**: `OrgTrackcrudsParametrizacion/app/Models/Envio.php`

**Mismos cambios que API**: campos y relaciones

---

## 🔄 Flujo Completo del Sistema

```
┌─────────────────────────────────────────────────────────────────┐
│                        1. CREACIÓN (AgroNexus)                   │
└─────────────────────────────────────────────────────────────────┘
                                    │
                Usuario de AgroNexus crea envío
                ├─ Selecciona insumos
                ├─ Selecciona dirección
                ├─ Selecciona tipo de vehículo (opcional)
                └─ Envía datos a la API
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                     2. ALMACENAMIENTO (API)                      │
└─────────────────────────────────────────────────────────────────┘
                                    │
                Guarda en base de datos
                ├─ estado_aprobacion = 'pendiente'
                ├─ id_tipo_vehiculo (si fue seleccionado)
                └─ Todos los productos/insumos
                                    │
                                    ▼
┌─────────────────────────────────────────────────────────────────┐
│                    3. VISUALIZACIÓN (OrgTrack)                   │
└─────────────────────────────────────────────────────────────────┘
                                    │
                Administrador ve envío pendiente
                ├─ Ve todos los detalles
                ├─ Ve tipo de vehículo solicitado
                └─ Decide: ¿Aprobar o Rechazar?
                                    │
                        ┌───────────┴───────────┐
                        │                       │
                    APROBAR                 RECHAZAR
                        │                       │
                        ▼                       ▼
        ┌───────────────────────┐   ┌──────────────────────┐
        │ 4a. APROBAR           │   │ 4b. RECHAZAR         │
        │ - Asigna transportista│   │ - Escribe motivo     │
        │ - Estado = 'aprobado' │   │ - Estado = 'rechazado'│
        │ - Sincroniza con API  │   │ - Sincroniza con API │
        └───────────────────────┘   └──────────────────────┘
                        │
                        ▼
        ┌───────────────────────────────────┐
        │ 5. NOTIFICACIÓN (Futuro)          │
        │ - Email a cliente de AgroNexus    │
        │ - Puede ver estado en su sistema  │
        └───────────────────────────────────┘
```

---

## 📊 Estados del Sistema

### Estado de Aprobación

| Estado | Color | Icono | Descripción |
|--------|-------|-------|-------------|
| `pendiente` | 🟡 Amarillo | ⏰ | Esperando decisión de OrgTrack |
| `aprobado` | 🟢 Verde | ✅ | Aprobado y transportista asignado |
| `rechazado` | 🔴 Rojo | ❌ | Rechazado con motivo |

---

## 🗄️ Estructura de Base de Datos

### Tabla `envios` (campos nuevos)

```sql
id_tipo_vehiculo          BIGINT UNSIGNED NULL
id_transportista_asignado BIGINT UNSIGNED NULL
estado_aprobacion         VARCHAR(255) DEFAULT 'pendiente'
motivo_rechazo            TEXT NULL

FOREIGN KEY (id_tipo_vehiculo) REFERENCES tipos_vehiculo(id)
FOREIGN KEY (id_transportista_asignado) REFERENCES usuarios(id)
```

---

## 🧪 Cómo Probar

### 1. Ejecutar Migración

```bash
cd "C:\Users\Personal\Downloads\OrgTrack + API\Api_Logistica"
php artisan migrate
```

### 2. Iniciar Servidores

```bash
# API (Puerto 8001)
cd "C:\Users\Personal\Downloads\OrgTrack + API\Api_Logistica"
php artisan serve --port=8001

# OrgTrack (Puerto que tengas configurado)
cd "C:\Users\Personal\Downloads\OrgTrack + API\OrgTrackcrudsParametrizacion"
php artisan serve

# AgroNexus (Puerto 8000)
cd "C:\Users\Personal\Downloads\productores\AgroNexus"
php artisan serve --port=8000
```

### 3. Flujo de Prueba

#### Paso 1: Crear Envío en AgroNexus
1. Ir a `http://localhost:8000/envios/create`
2. Llenar el formulario:
   - Seleccionar insumos
   - Seleccionar dirección
   - **NUEVO**: Seleccionar tipo de vehículo
3. Guardar envío
4. Verificar que se guardó correctamente

#### Paso 2: Ver en OrgTrack
1. Ir a la sección de envíos en OrgTrack
2. Verificar que aparece el envío con estado "Pendiente Aprobación"
3. Ver que aparecen botones de Aprobar y Rechazar

#### Paso 3: Aprobar Envío
1. Hacer clic en el botón verde de Aprobar
2. En el modal, seleccionar un transportista
3. Confirmar
4. Verificar que el estado cambió a "Aprobado"
5. Verificar que desaparecieron los botones de acción

#### Paso 4: Ver Detalles
1. Hacer clic en el ícono de Ver (ojo)
2. Verificar que se muestra:
   - Estado de Aprobación
   - Tipo de Vehículo
   - Transportista Asignado
   - Fechas correctamente

#### Paso 5 (Opcional): Probar Rechazo
1. Crear otro envío desde AgroNexus
2. En OrgTrack, hacer clic en el botón rojo de Rechazar
3. Escribir un motivo (mín. 10 caracteres)
4. Confirmar
5. Verificar que el estado cambió a "Rechazado"
6. Ver que se muestra el motivo del rechazo

---

## 📝 Archivos Modificados/Creados

### API Logística
- ✨ `app/Models/TipoVehiculo.php` (NUEVO)
- ✨ `app/Http/Controllers/Api/TipoVehiculoController.php` (NUEVO)
- ✨ `database/migrations/2025_11_30_000001_add_vehiculo_and_transportista_to_envios.php` (NUEVO)
- 📝 `app/Models/Envio.php` (MODIFICADO)
- 📝 `app/Http/Controllers/Api/EnvioController.php` (MODIFICADO)
- 📝 `routes/api.php` (MODIFICADO)

### AgroNexus
- 📝 `app/Http/Controllers/Web/EnvioController.php` (MODIFICADO)
- 📝 `resources/views/envios/create.blade.php` (MODIFICADO)

### OrgTrack
- 📝 `app/Models/Envio.php` (MODIFICADO)
- 📝 `app/Http/Controllers/EnvioController.php` (MODIFICADO)
- 📝 `resources/views/envios/index.blade.php` (MODIFICADO)
- 📝 `resources/views/envios/show.blade.php` (MODIFICADO)
- 📝 `routes/web.php` (MODIFICADO)

---

## ⚠️ Consideraciones Importantes

### 1. Sincronización
- Los cambios de aprobación/rechazo se sincronizan entre OrgTrack y la API
- Si la API no está disponible, se registra un warning en logs pero la operación continúa localmente

### 2. Roles
- El selector de transportistas solo muestra usuarios con rol "Transportista"
- Asegúrate de tener usuarios con ese rol en la BD de OrgTrack

### 3. Validaciones
- El motivo de rechazo debe tener mínimo 10 caracteres
- El transportista es obligatorio al aprobar
- No se puede aprobar/rechazar un envío que ya fue procesado

### 4. Estados
- Una vez aprobado o rechazado, los botones desaparecen
- No hay opción de "revertir" una decisión (por diseño)

---

## 🚀 Próximas Mejoras Sugeridas

1. **Notificaciones**: Enviar email a AgroNexus cuando se aprueba/rechaza
2. **Dashboard**: Estadísticas de envíos pendientes/aprobados/rechazados
3. **Filtros**: Filtrar envíos por estado de aprobación
4. **Historial**: Registrar quién aprobó/rechazó y cuándo
5. **Edición**: Permitir cambiar transportista asignado después de aprobar

---

Fecha: 30 de Noviembre, 2025

