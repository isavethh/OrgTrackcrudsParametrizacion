# Guía de Refactorización - Migración a Nuevo Esquema PostgreSQL

## ✅ CAMBIOS REALIZADOS

### 1. Modelos Actualizados

#### Usuario
- ✅ Se movieron los campos `nombre`, `apellido`, `ci`, `telefono` desde `Persona` directamente a `Usuario`
- ✅ Se eliminó la relación `belongsTo(Persona)`
- ✅ Se actualizó el fillable para incluir los nuevos campos

#### Transportista
- ✅ Se mantiene la tabla `transportistas` con campos: `ci`, `telefono`, `id_estado_transportista`
- ✅ Relaciones actualizadas con `EstadoTransportista` y `AsignacionMultiple`

#### Vehiculo
- ✅ Se mantiene la tabla `vehiculos` con campos existentes
- ✅ Relaciones con `TipoVehiculo`, `EstadoVehiculo` y `AsignacionMultiple`

#### Envio
- ✅ Se simplificó el modelo para usar campos básicos: `id_usuario`, `fecha_creacion`, `fecha_inicio`, `fecha_entrega`, `id_direccion`
- ✅ Se mantienen relaciones con `Usuario`, `Direccion`, `AsignacionMultiple`, `HistorialEstado`

#### AsignacionMultiple
- ✅ Se agregaron todos los campos del nuevo esquema: `id_envio`, `id_transportista`, `id_vehiculo`, `id_recogida_entrega`, `id_tipo_transporte`, `id_estado_asignacion`
- ✅ Se agregaron relaciones con: `Envio`, `Transportista`, `Vehiculo`, `RecogidaEntrega`, `TipoTransporte`, `EstadoAsignacionMultiple`, `Carga` (belongsToMany), `ChecklistCondicion`, `IncidenteTransporte`, `FirmaEnvio`, `FirmaTransportista`, `QrToken`

#### QrToken
- ✅ Se actualizó para usar `id_asignacion` en lugar de `cliente_id`
- ✅ Se agregaron campos: `id_estado_qrtoken`, `imagenqr`, `fecha_creacion`
- ✅ Relaciones con `AsignacionMultiple` y `EstadoQrToken`

### 2. Nuevos Modelos Creados

- ✅ `CatalogoCarga` - Catálogo de tipos de carga
- ✅ `Carga` - Carga específica con cantidad y peso
- ✅ `RecogidaEntrega` - Detalles de recogida y entrega
- ✅ `CondicionTransporte` - Condiciones de transporte para checklist
- ✅ `TipoIncidenteTransporte` - Tipos de incidentes
- ✅ `EstadoQrToken` - Estados para tokens QR
- ✅ `EstadoAsignacionMultiple` - Estados para asignaciones
- ✅ `ChecklistCondicion` - Checklist principal
- ✅ `ChecklistCondicionDetalle` - Detalle de checklist con condiciones
- ✅ `IncidenteTransporte` - Incidentes durante transporte
- ✅ `FirmaEnvio` - Firma de recepción de envío
- ✅ `FirmaTransportista` - Firma del transportista
- ✅ `DireccionSegmento` - Segmentos de ruta
- ✅ `HistorialEstado` - Historial de estados de envío
- ✅ `EstadoEnvio` - Estados de envío
- ✅ `RolUsuario` - Roles de usuario
- ✅ `TipoVehiculo` - Tipos de vehículo (ya existía)
- ✅ `EstadoVehiculo` - Estados de vehículo (ya existía)

### 3. Controladores Actualizados

#### AdminController
- ✅ Se eliminó la dependencia del modelo `Persona`
- ✅ Los métodos `store` y `update` ahora crean/actualizan usuarios con campos de persona directamente
- ✅ Validaciones actualizadas para `unique:usuarios,ci` en lugar de `unique:persona,ci`
- ✅ Método `destroy` simplificado (no necesita eliminar persona por separado)

#### ClienteController
- ✅ Se eliminó la dependencia del modelo `Persona`
- ✅ Los métodos `store` y `update` ahora crean/actualizan usuarios con campos de persona directamente
- ✅ Validaciones actualizadas para `unique:usuarios,ci` en lugar de `unique:persona,ci`
- ✅ Método `destroy` simplificado

### 4. Vistas Actualizadas

#### Vistas de Admins
- ✅ `index.blade.php`: Cambio de `$admin->persona->nombre` a `$admin->nombre`
- ✅ `edit.blade.php`: Cambio de `$admin->persona->nombre` a `$admin->nombre` (y similares)

#### Vistas de Clientes
- ✅ `index.blade.php`: Cambio de `$cliente->persona->nombre` a `$cliente->nombre`
- ✅ `edit.blade.php`: Cambio de `$cliente->usuario->nombre` a `$cliente->nombre`

## 🗄️ MIGRACIÓN DE BASE DE DATOS

### Opción 1: Base de datos nueva (RECOMENDADO para desarrollo)

Si estás empezando desde cero o en ambiente de desarrollo:

```bash
# 1. Conectar a PostgreSQL
psql -U postgres

# 2. Eliminar y recrear la base de datos
DROP DATABASE IF EXISTS "LogisticaOrg";
CREATE DATABASE "LogisticaOrg";

# 3. Conectar a la base de datos
\c LogisticaOrg

# 4. Ejecutar el nuevo schema
\i database/schema_nuevo.sql

# 5. Salir
\q
```

### Opción 2: Migrar base de datos existente

Si tienes datos existentes que quieres conservar:

```bash
# 1. Hacer backup de la base de datos actual
pg_dump -U postgres LogisticaOrg > backup_antes_migracion.sql

# 2. Conectar a la base de datos
psql -U postgres LogisticaOrg

# 3. Ejecutar script de migración
\i database/migracion_persona_a_usuario.sql

# 4. Verificar que la migración fue exitosa
SELECT id, correo, nombre, apellido, ci FROM usuarios LIMIT 5;

# 5. Salir
\q
```

## ⚠️ CONTROLADORES PENDIENTES DE ACTUALIZACIÓN

Los siguientes controladores necesitan ser actualizados manualmente según la nueva lógica de negocio:

### TransportistaController
- ❌ Pendiente: Agregar manejo de campos si se agregan `usuario_id` y `placa` al modelo

### VehiculoController
- ❌ Pendiente: Si necesitas agregar campos como `admin_id`, `tipo_transporte_id`, `tamano_transporte_id`, `marca`, `modelo`

### EnvioController
- ❌ Pendiente: Actualizar para manejar la tabla `carga` y `asignacioncarga` en lugar de `envio_productos`
- ❌ Pendiente: Agregar lógica para `RecogidaEntrega`, `ChecklistCondicion`, etc.

### DireccionController
- ❌ Pendiente: Actualizar si necesitas manejar `direccionsegmento` o cambiar la estructura de origen/destino

### QRController
- ❌ Pendiente: Actualizar para usar `id_asignacion` en lugar de `cliente_id`
- ❌ Pendiente: Implementar lógica de estados QR (`EstadoQrToken`)
- ❌ Pendiente: Agregar manejo de firmas (`FirmaEnvio`, `FirmaTransportista`)

## 📝 NOTAS IMPORTANTES

1. **Modelo Persona**: El modelo `Persona` ya no se usa pero se mantiene el archivo por compatibilidad. Se puede eliminar después de confirmar que todo funciona.

2. **Validaciones**: Todas las validaciones de `unique:persona,ci` se cambiaron a `unique:usuarios,ci`.

3. **Relaciones**: Se eliminaron todas las referencias a `$usuario->persona->nombre` y ahora se usa `$usuario->nombre` directamente.

4. **Base de datos**: El nuevo esquema está en `database/schema_nuevo.sql` y el script de migración en `database/migracion_persona_a_usuario.sql`.

5. **Seeders**: Los seeders existentes necesitan ser actualizados para el nuevo esquema. Se recomienda crear nuevos seeders para las tablas de catálogo.

## 🧪 TESTING

Después de la migración, verifica:

1. ✅ Login de usuarios funciona correctamente
2. ✅ CRUD de Administradores funciona
3. ✅ CRUD de Clientes funciona
4. ⚠️ CRUD de Transportistas (verificar después de actualizar controller)
5. ⚠️ CRUD de Vehículos (verificar después de actualizar controller)
6. ⚠️ CRUD de Envíos (verificar después de actualizar controller)
7. ⚠️ Generación de QR (verificar después de actualizar controller)

## 🚀 PRÓXIMOS PASOS

1. Ejecutar la migración de base de datos según tu escenario (nueva o existente)
2. Probar login y CRUDs de Admin y Cliente
3. Actualizar los controladores pendientes según tus necesidades específicas
4. Actualizar seeders para las nuevas tablas de catálogo
5. Actualizar vistas que muestran datos de envíos, transportistas, etc.
6. Testing completo de todas las funcionalidades

## 📞 SOPORTE

Si encuentras algún error después de la migración:

1. Revisa los logs de Laravel: `storage/logs/laravel.log`
2. Verifica la consola del navegador para errores JavaScript
3. Usa `php artisan route:list` para ver todas las rutas disponibles
4. Usa `php artisan tinker` para probar modelos y relaciones

---

**Fecha de refactorización**: 25 de noviembre, 2025
**Estado**: ✅ Modelos y controladores principales actualizados | ⚠️ Controladores secundarios pendientes
