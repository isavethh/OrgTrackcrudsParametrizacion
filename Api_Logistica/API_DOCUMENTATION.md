# API Logística - Documentación

## Descripción General

API centralizada para la gestión de envíos entre los sistemas **AgroNexus** y **OrgTrack**. 

- **URL Base**: `http://localhost:8001/api`
- **Formato de respuesta**: JSON
- **Autenticación**: No requerida actualmente (se puede agregar Sanctum en el futuro)

---

## Endpoints

### Health Check

Verifica que la API esté funcionando correctamente.

**Endpoint**: `GET /health`

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "message": "API Logística funcionando correctamente",
    "version": "1.0.0"
}
```

---

## Direcciones

### 1. Listar todas las direcciones

**Endpoint**: `GET /direcciones`

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "nombreorigen": "Calle 1, La Paz",
            "origen_lng": "-68.11929700",
            "origen_lat": "-16.50000000",
            "nombredestino": "Av. Principal, El Alto",
            "destino_lng": "-68.16320000",
            "destino_lat": "-16.50500000",
            "rutageojson": null,
            "created_at": "2025-11-29T05:30:00.000000Z",
            "updated_at": "2025-11-29T05:30:00.000000Z"
        }
    ]
}
```

### 2. Crear una dirección

**Endpoint**: `POST /direcciones`

**Parámetros**:
- `nombreorigen` (string, requerido): Nombre del punto de origen
- `origen_lat` (decimal, requerido): Latitud del origen (-90 a 90)
- `origen_lng` (decimal, requerido): Longitud del origen (-180 a 180)
- `nombredestino` (string, requerido): Nombre del punto de destino
- `destino_lat` (decimal, requerido): Latitud del destino
- `destino_lng` (decimal, requerido): Longitud del destino
- `rutageojson` (string, opcional): Datos GeoJSON de la ruta

**Ejemplo de petición**:
```json
{
    "nombreorigen": "Almacén Central, La Paz",
    "origen_lat": -16.5000,
    "origen_lng": -68.1193,
    "nombredestino": "Mercado Rodríguez, La Paz",
    "destino_lat": -16.4955,
    "destino_lng": -68.1336,
    "rutageojson": "{\"type\":\"LineString\",\"coordinates\":[...]}"
}
```

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "message": "Dirección creada exitosamente",
    "data": {
        "id": 2,
        "nombreorigen": "Almacén Central, La Paz",
        "origen_lng": "-68.11930000",
        "origen_lat": "-16.50000000",
        "nombredestino": "Mercado Rodríguez, La Paz",
        "destino_lng": "-68.13360000",
        "destino_lat": "-16.49550000",
        "rutageojson": "{\"type\":\"LineString\",\"coordinates\":[...]}",
        "created_at": "2025-11-29T06:00:00.000000Z",
        "updated_at": "2025-11-29T06:00:00.000000Z"
    }
}
```

### 3. Ver una dirección específica

**Endpoint**: `GET /direcciones/{id}`

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "nombreorigen": "Calle 1, La Paz",
        "origen_lng": "-68.11929700",
        "origen_lat": "-16.50000000",
        "nombredestino": "Av. Principal, El Alto",
        "destino_lng": "-68.16320000",
        "destino_lat": "-16.50500000",
        "rutageojson": null,
        "created_at": "2025-11-29T05:30:00.000000Z",
        "updated_at": "2025-11-29T05:30:00.000000Z"
    }
}
```

### 4. Actualizar una dirección

**Endpoint**: `PUT /direcciones/{id}`

**Parámetros**: Mismos que crear dirección

### 5. Eliminar una dirección

**Endpoint**: `DELETE /direcciones/{id}`

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "message": "Dirección eliminada exitosamente"
}
```

---

## Envíos

### 1. Listar todos los envíos

**Endpoint**: `GET /envios`

**Parámetros opcionales (query string)**:
- `sistema_origen`: Filtrar por sistema (`agronexus` o `orgtrack`)
- `estado`: Filtrar por estado (`pendiente`, `en_transito`, `entregado`, `cancelado`)

**Ejemplos**:
- `GET /envios` - Todos los envíos
- `GET /envios?sistema_origen=agronexus` - Solo envíos de AgroNexus
- `GET /envios?estado=pendiente` - Solo envíos pendientes

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "usuario_nombre": "Juan Pérez",
            "sistema_origen": "agronexus",
            "direccion_id": 1,
            "fecha_creacion": "2025-11-29T05:30:00.000000Z",
            "fecha_entrega_aproximada": "2025-11-30",
            "hora_entrega_aproximada": "14:00:00",
            "peso_total_envio": "150.50",
            "costo_total_envio": "450.00",
            "estado": "pendiente",
            "estado_label": "Pendiente",
            "observaciones": null,
            "created_at": "2025-11-29T05:30:00.000000Z",
            "updated_at": "2025-11-29T05:30:00.000000Z",
            "direccion": {
                "id": 1,
                "nombreorigen": "Calle 1, La Paz",
                "origen_lng": "-68.11929700",
                "origen_lat": "-16.50000000",
                "nombredestino": "Av. Principal, El Alto",
                "destino_lng": "-68.16320000",
                "destino_lat": "-16.50500000",
                "rutageojson": null
            },
            "insumos": [
                {
                    "id": 1,
                    "envio_id": 1,
                    "nombre_insumo": "Fertilizante Orgánico",
                    "tipo_insumo": "Fertilizantes",
                    "cantidad": 10,
                    "peso_por_unidad": "5.00",
                    "peso_total": "50.00",
                    "costo_unitario": "15.00",
                    "costo_total": "150.00",
                    "tipo_empaque": "Bolsa Plástica",
                    "unidad_medida": "Kilogramo"
                }
            ]
        }
    ]
}
```

### 2. Crear un envío

**Endpoint**: `POST /envios`

**Parámetros**:
- `usuario_nombre` (string, requerido): Nombre completo del usuario que realiza el envío
- `sistema_origen` (string, requerido): Sistema de origen (`agronexus` o `orgtrack`)
- `direccion_id` (integer, requerido): ID de la dirección existente
- `fecha_entrega_aproximada` (date, opcional): Fecha aproximada de entrega (formato: YYYY-MM-DD)
- `hora_entrega_aproximada` (time, opcional): Hora aproximada de entrega (formato: HH:mm)
- `observaciones` (string, opcional): Observaciones adicionales
- `insumos` (array, requerido): Lista de insumos/productos
  - `nombre_insumo` (string, requerido): Nombre del insumo/producto
  - `tipo_insumo` (string, opcional): Tipo o categoría del insumo
  - `cantidad` (integer, requerido): Cantidad de unidades
  - `peso_por_unidad` (decimal, requerido): Peso por unidad en kg
  - `costo_unitario` (decimal, requerido): Costo por unidad en Bs.
  - `tipo_empaque` (string, opcional): Tipo de empaque
  - `unidad_medida` (string, opcional): Unidad de medida

**Ejemplo de petición desde AgroNexus**:
```json
{
    "usuario_nombre": "Juan Pérez García",
    "sistema_origen": "agronexus",
    "direccion_id": 1,
    "fecha_entrega_aproximada": "2025-12-01",
    "hora_entrega_aproximada": "14:30",
    "observaciones": "Entregar en la mañana",
    "insumos": [
        {
            "nombre_insumo": "Fertilizante Orgánico",
            "tipo_insumo": "Fertilizantes",
            "cantidad": 10,
            "peso_por_unidad": 5.00,
            "costo_unitario": 15.00,
            "tipo_empaque": "Bolsa Plástica",
            "unidad_medida": "Kilogramo"
        },
        {
            "nombre_insumo": "Semillas de Maíz",
            "tipo_insumo": "Semillas",
            "cantidad": 5,
            "peso_por_unidad": 2.00,
            "costo_unitario": 25.00,
            "tipo_empaque": "Caja de Cartón",
            "unidad_medida": "Kilogramo"
        }
    ]
}
```

**Ejemplo de petición desde OrgTrack**:
```json
{
    "usuario_nombre": "María López Silva",
    "sistema_origen": "orgtrack",
    "direccion_id": 2,
    "fecha_entrega_aproximada": "2025-12-02",
    "hora_entrega_aproximada": "10:00",
    "insumos": [
        {
            "nombre_insumo": "Tomate",
            "tipo_insumo": "Verduras",
            "cantidad": 50,
            "peso_por_unidad": 0.20,
            "costo_unitario": 3.50,
            "tipo_empaque": "Caja de Cartón",
            "unidad_medida": "Kilogramo"
        }
    ]
}
```

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "message": "Envío creado exitosamente",
    "data": {
        "id": 2,
        "usuario_nombre": "Juan Pérez García",
        "sistema_origen": "agronexus",
        "direccion_id": 1,
        "fecha_creacion": "2025-11-29T07:00:00.000000Z",
        "fecha_entrega_aproximada": "2025-12-01",
        "hora_entrega_aproximada": "14:30:00",
        "peso_total_envio": "60.00",
        "costo_total_envio": "275.00",
        "estado": "pendiente",
        "estado_label": "Pendiente",
        "observaciones": "Entregar en la mañana",
        "direccion": {
            "id": 1,
            "nombreorigen": "Calle 1, La Paz",
            "nombredestino": "Av. Principal, El Alto"
        },
        "insumos": [
            {
                "id": 2,
                "nombre_insumo": "Fertilizante Orgánico",
                "tipo_insumo": "Fertilizantes",
                "cantidad": 10,
                "peso_total": "50.00",
                "costo_total": "150.00"
            },
            {
                "id": 3,
                "nombre_insumo": "Semillas de Maíz",
                "tipo_insumo": "Semillas",
                "cantidad": 5,
                "peso_total": "10.00",
                "costo_total": "125.00"
            }
        ]
    }
}
```

**Respuesta de error (validación)**:
```json
{
    "success": false,
    "message": "Error de validación",
    "errors": {
        "usuario_nombre": ["El campo usuario nombre es obligatorio."],
        "insumos.0.cantidad": ["La cantidad debe ser al menos 1."]
    }
}
```

### 3. Ver un envío específico

**Endpoint**: `GET /envios/{id}`

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "data": {
        "id": 1,
        "usuario_nombre": "Juan Pérez",
        "sistema_origen": "agronexus",
        "direccion_id": 1,
        "fecha_creacion": "2025-11-29T05:30:00.000000Z",
        "fecha_entrega_aproximada": "2025-11-30",
        "hora_entrega_aproximada": "14:00:00",
        "peso_total_envio": "150.50",
        "costo_total_envio": "450.00",
        "estado": "pendiente",
        "estado_label": "Pendiente",
        "observaciones": null,
        "direccion": {
            "id": 1,
            "nombreorigen": "Calle 1, La Paz",
            "origen_lng": "-68.11929700",
            "origen_lat": "-16.50000000",
            "nombredestino": "Av. Principal, El Alto",
            "destino_lng": "-68.16320000",
            "destino_lat": "-16.50500000"
        },
        "insumos": [
            {
                "id": 1,
                "envio_id": 1,
                "nombre_insumo": "Fertilizante Orgánico",
                "tipo_insumo": "Fertilizantes",
                "cantidad": 10,
                "peso_por_unidad": "5.00",
                "peso_total": "50.00",
                "costo_unitario": "15.00",
                "costo_total": "150.00",
                "tipo_empaque": "Bolsa Plástica",
                "unidad_medida": "Kilogramo"
            }
        ]
    }
}
```

### 4. Actualizar un envío

**Endpoint**: `PUT /envios/{id}`

**Parámetros** (todos opcionales, se actualizan solo los que se envían):
- `direccion_id` (integer): Nueva dirección
- `fecha_entrega_aproximada` (date): Nueva fecha de entrega
- `hora_entrega_aproximada` (time): Nueva hora de entrega
- `estado` (string): Nuevo estado (`pendiente`, `en_transito`, `entregado`, `cancelado`)
- `observaciones` (string): Nuevas observaciones
- `insumos` (array): Nueva lista de insumos (reemplaza la lista anterior)

**Ejemplo - Actualizar estado**:
```json
{
    "estado": "en_transito",
    "observaciones": "El envío está en camino"
}
```

**Ejemplo - Actualizar insumos completos**:
```json
{
    "insumos": [
        {
            "nombre_insumo": "Fertilizante Orgánico",
            "tipo_insumo": "Fertilizantes",
            "cantidad": 15,
            "peso_por_unidad": 5.00,
            "costo_unitario": 15.00,
            "tipo_empaque": "Bolsa Plástica",
            "unidad_medida": "Kilogramo"
        }
    ]
}
```

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "message": "Envío actualizado exitosamente",
    "data": {
        "id": 1,
        "estado": "en_transito",
        "estado_label": "En Tránsito",
        "observaciones": "El envío está en camino",
        "peso_total_envio": "75.00",
        "costo_total_envio": "225.00"
    }
}
```

### 5. Eliminar un envío

**Endpoint**: `DELETE /envios/{id}`

**Respuesta de ejemplo**:
```json
{
    "success": true,
    "message": "Envío eliminado exitosamente"
}
```

---

## Estados de Envío

Los envíos pueden tener los siguientes estados:

- **pendiente**: El envío ha sido creado pero aún no ha iniciado
- **en_transito**: El envío está en camino al destino
- **entregado**: El envío ha sido entregado exitosamente
- **cancelado**: El envío ha sido cancelado

---

## Códigos de Estado HTTP

- `200 OK`: Operación exitosa (GET, PUT)
- `201 Created`: Recurso creado exitosamente (POST)
- `422 Unprocessable Entity`: Error de validación
- `404 Not Found`: Recurso no encontrado
- `500 Internal Server Error`: Error del servidor

---

## Ejemplos de Uso desde Laravel

### Desde AgroNexus o OrgTrack

```php
use Illuminate\Support\Facades\Http;

// Crear dirección
$direccion = Http::post('http://localhost:8001/api/direcciones', [
    'nombreorigen' => 'Origen',
    'origen_lat' => -16.5,
    'origen_lng' => -68.12,
    'nombredestino' => 'Destino',
    'destino_lat' => -16.48,
    'destino_lng' => -68.15,
]);

// Crear envío
$envio = Http::post('http://localhost:8001/api/envios', [
    'usuario_nombre' => 'Juan Pérez',
    'sistema_origen' => 'agronexus',
    'direccion_id' => 1,
    'fecha_entrega_aproximada' => '2025-12-01',
    'hora_entrega_aproximada' => '14:30',
    'insumos' => [
        [
            'nombre_insumo' => 'Fertilizante',
            'tipo_insumo' => 'Fertilizantes',
            'cantidad' => 10,
            'peso_por_unidad' => 5.0,
            'costo_unitario' => 15.0,
            'tipo_empaque' => 'Bolsa',
            'unidad_medida' => 'Kg',
        ]
    ],
]);

if ($envio->successful()) {
    $data = $envio->json('data');
    // Procesar datos
}

// Listar envíos de un sistema
$envios = Http::get('http://localhost:8001/api/envios', [
    'sistema_origen' => 'agronexus'
]);

// Actualizar estado
$actualizar = Http::put('http://localhost:8001/api/envios/1', [
    'estado' => 'en_transito'
]);
```

---

## Notas Importantes

1. **Sincronización**: Los sistemas AgroNexus y OrgTrack envían datos a esta API y también mantienen una copia local para funcionalidades específicas (como tracking en OrgTrack).

2. **Formato de datos**: 
   - Las fechas deben estar en formato ISO 8601: `YYYY-MM-DD`
   - Las horas en formato 24h: `HH:mm`
   - Los decimales pueden usar punto como separador
   - El nombre del usuario debe enviarse completo desde el sistema origen

3. **Tolerancia a fallos**: Los sistemas cliente deben implementar manejo de errores en caso de que la API no esté disponible.

4. **Validación**: La API valida todos los campos requeridos y retorna errores detallados en caso de datos inválidos.

5. **Base de datos**: La API usa PostgreSQL y está configurada para correr en el puerto 8001 por defecto.

---

## Configuración e Instalación

### Prerrequisitos
- PHP 8.2+
- PostgreSQL
- Composer

### Instalación

1. Navegar al directorio de la API:
```bash
cd Api_Logistica
```

2. Instalar dependencias:
```bash
composer install
```

3. Configurar base de datos en `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=api_logistica
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

4. Crear base de datos:
```bash
createdb api_logistica
```

5. Ejecutar migraciones:
```bash
php artisan migrate
```

6. Iniciar servidor:
```bash
php artisan serve --port=8001
```

La API estará disponible en `http://localhost:8001/api`

---

## Pruebas

Verificar que la API funcione:

```bash
curl http://localhost:8001/api/health
```

Respuesta esperada:
```json
{
    "success": true,
    "message": "API Logística funcionando correctamente",
    "version": "1.0.0"
}
```

---

## Próximas Funcionalidades

- Autenticación con Laravel Sanctum
- Webhooks para notificaciones
- Paginación en listados
- Filtros avanzados
- Estadísticas y reportes
- Integración con servicios de mapas para rutas optimizadas

