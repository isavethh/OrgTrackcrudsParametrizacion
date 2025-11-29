# API Logística

API centralizada para la gestión de envíos entre los sistemas **AgroNexus** y **OrgTrack**.

## 📋 Descripción

Esta API REST proporciona una plataforma unificada para gestionar envíos de productos e insumos entre dos sistemas principales:

- **AgroNexus**: Sistema de gestión agrícola (envío de insumos)
- **OrgTrack**: Sistema de logística y tracking (envío de productos)

La API centraliza la información de envíos, direcciones y productos, permitiendo que ambos sistemas se comuniquen de manera eficiente y mantengan datos sincronizados.

## 🚀 Características

- ✅ **Gestión de Direcciones**: CRUD completo de rutas origen-destino con coordenadas GPS
- ✅ **Gestión de Envíos**: Crear, listar, actualizar y eliminar envíos
- ✅ **Multi-sistema**: Soporte para múltiples sistemas origen (AgroNexus, OrgTrack)
- ✅ **Estados de Envío**: Tracking de estados (pendiente, en tránsito, entregado, cancelado)
- ✅ **Productos/Insumos**: Gestión detallada de items en cada envío
- ✅ **Información de Usuario**: Registro del usuario que realiza cada envío
- ✅ **Cálculos Automáticos**: Peso total y costo total calculados automáticamente
- ✅ **API RESTful**: Endpoints estándar con respuestas JSON

## 📦 Requisitos

- PHP 8.2 o superior
- PostgreSQL 12 o superior
- Composer
- Laravel 11

## 🛠️ Instalación

1. **Clonar el repositorio** (si aplica) o navegar al directorio:
```bash
cd Api_Logistica
```

2. **Instalar dependencias**:
```bash
composer install
```

3. **Configurar variables de entorno**:
```bash
cp .env.example .env
```

Editar `.env` con las credenciales de tu base de datos:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=api_logistica
DB_USERNAME=postgres
DB_PASSWORD=tu_password
```

4. **Generar clave de aplicación**:
```bash
php artisan key:generate
```

5. **Crear base de datos**:
```bash
createdb api_logistica
```

O desde psql:
```sql
CREATE DATABASE api_logistica;
```

6. **Ejecutar migraciones**:
```bash
php artisan migrate
```

7. **Iniciar servidor de desarrollo**:
```bash
php artisan serve --port=8001
```

La API estará disponible en: `http://localhost:8001/api`

## 🔍 Verificación

Prueba que la API funcione correctamente:

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

## 📖 Documentación

Consulta la documentación completa de la API en: [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)

### Endpoints Principales

#### Direcciones
- `GET /api/direcciones` - Listar todas las direcciones
- `POST /api/direcciones` - Crear una dirección
- `GET /api/direcciones/{id}` - Ver una dirección
- `PUT /api/direcciones/{id}` - Actualizar una dirección
- `DELETE /api/direcciones/{id}` - Eliminar una dirección

#### Envíos
- `GET /api/envios` - Listar todos los envíos
- `GET /api/envios?sistema_origen=agronexus` - Filtrar por sistema
- `GET /api/envios?estado=pendiente` - Filtrar por estado
- `POST /api/envios` - Crear un envío
- `GET /api/envios/{id}` - Ver un envío
- `PUT /api/envios/{id}` - Actualizar un envío
- `DELETE /api/envios/{id}` - Eliminar un envío

### Ejemplo de Uso

**Crear un envío desde AgroNexus:**

```bash
curl -X POST http://localhost:8001/api/envios \
  -H "Content-Type: application/json" \
  -d '{
    "usuario_nombre": "Juan Pérez",
    "sistema_origen": "agronexus",
    "direccion_id": 1,
    "fecha_entrega_aproximada": "2025-12-01",
    "hora_entrega_aproximada": "14:30",
    "insumos": [
      {
        "nombre_insumo": "Fertilizante Orgánico",
        "tipo_insumo": "Fertilizantes",
        "cantidad": 10,
        "peso_por_unidad": 5.00,
        "costo_unitario": 15.00,
        "tipo_empaque": "Bolsa Plástica",
        "unidad_medida": "Kilogramo"
      }
    ]
  }'
```

## 🏗️ Estructura del Proyecto

```
Api_Logistica/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       └── Api/
│   │           ├── DireccionController.php
│   │           └── EnvioController.php
│   └── Models/
│       ├── Direccion.php
│       ├── Envio.php
│       └── EnvioInsumo.php
├── database/
│   └── migrations/
│       ├── 2025_11_29_051903_create_direccion_table.php
│       ├── 2025_11_29_051923_create_envios_table.php
│       └── 2025_11_29_051924_create_envio_insumos_table.php
├── routes/
│   └── api.php
├── API_DOCUMENTATION.md
└── README.md
```

## 🔗 Integración con Otros Sistemas

### AgroNexus

AgroNexus envía información de envíos de insumos agrícolas:

```php
use Illuminate\Support\Facades\Http;

$response = Http::post('http://localhost:8001/api/envios', [
    'usuario_nombre' => Auth::user()->nombre . ' ' . Auth::user()->apellido,
    'sistema_origen' => 'agronexus',
    'direccion_id' => $direccionId,
    'fecha_entrega_aproximada' => $fecha,
    'insumos' => $insumosArray,
]);
```

### OrgTrack

OrgTrack envía información de envíos de productos (frutas y verduras):

```php
use Illuminate\Support\Facades\Http;

$response = Http::post('http://localhost:8001/api/envios', [
    'usuario_nombre' => $usuario->nombre . ' ' . $usuario->apellido,
    'sistema_origen' => 'orgtrack',
    'direccion_id' => $direccionId,
    'fecha_entrega_aproximada' => $fecha,
    'insumos' => $productosArray,
]);
```

## 🗃️ Base de Datos

### Tablas Principales

- **direccion**: Almacena rutas con origen y destino (coordenadas GPS)
- **envios**: Información principal de cada envío
- **envio_insumos**: Productos/insumos incluidos en cada envío

### Diagrama de Relaciones

```
direccion (1) ----< (N) envios (1) ----< (N) envio_insumos
```

## 🔐 Seguridad

Actualmente la API no requiere autenticación. Para entornos de producción se recomienda:

1. Implementar Laravel Sanctum para autenticación por tokens
2. Configurar CORS adecuadamente
3. Usar HTTPS
4. Implementar rate limiting

## 🧪 Testing

```bash
php artisan test
```

## 📝 Licencia

Este proyecto es parte del sistema de logística integrado AgroNexus-OrgTrack.

## 👥 Integración Multi-Sistema

Esta API permite que múltiples sistemas compartan información de envíos de manera centralizada, manteniendo la trazabilidad y facilitando la gestión logística unificada.

- **AgroNexus** → Gestión de insumos agrícolas
- **OrgTrack** → Gestión de productos y tracking en tiempo real
- **API Logística** → Centro de datos unificado

---

Para más detalles técnicos, consulta [API_DOCUMENTATION.md](./API_DOCUMENTATION.md)
