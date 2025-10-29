# 📚 API ENDPOINTS DOCUMENTATION - OrgTrack Laravel

## 🔐 Autenticación
**Base URL:** `http://localhost:8000/api`  
**Middleware:** JWT en todas las rutas (excepto auth)

---

## 🔑 AUTH ENDPOINTS

### POST `/auth/register`
**Descripción:** Registrar nuevo usuario
```json
{
  "nombre": "Juan",
  "apellido": "Pérez",
  "correo": "juan@example.com",
  "contrasena": "password123",
  "rol": "cliente"
}
```
**Respuesta:**
```json
{
  "mensaje": "Usuario registrado exitosamente",
  "usuario": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "correo": "juan@example.com",
    "rol": "cliente"
  }
}
```

### POST `/auth/login`
**Descripción:** Iniciar sesión
```json
{
  "correo": "juan@example.com",
  "contrasena": "password123"
}
```
**Respuesta:**
```json
{
  "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
  "usuario": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "correo": "juan@example.com",
    "rol": "cliente"
  }
}
```

---

## 👥 USUARIOS ENDPOINTS

### GET `/usuarios`
**Descripción:** Obtener todos los usuarios (solo admin)
**Headers:** `Authorization: Bearer {token}`

### GET `/usuarios/{id}`
**Descripción:** Obtener usuario por ID
**Headers:** `Authorization: Bearer {token}`

### POST `/usuarios`
**Descripción:** Crear nuevo usuario
```json
{
  "nombre": "María",
  "apellido": "González",
  "correo": "maria@example.com",
  "contrasena": "password123",
  "rol": "transportista"
}
```

### PUT `/usuarios/{id}`
**Descripción:** Actualizar usuario
```json
{
  "nombre": "María",
  "apellido": "González",
  "correo": "maria@example.com",
  "rol": "transportista"
}
```

### DELETE `/usuarios/{id}`
**Descripción:** Eliminar usuario

### GET `/usuarios/clientes`
**Descripción:** Obtener solo clientes

### GET `/usuarios/rol/{rol}`
**Descripción:** Obtener usuarios por rol

### PUT `/usuarios/{id}/cambiar-rol`
**Descripción:** Cambiar rol de usuario (solo admin)
```json
{
  "rol": "transportista"
}
```
**Respuesta:**
```json
{
  "mensaje": "Rol actualizado correctamente",
  "usuario": {
    "id": 1,
    "nombre": "Juan",
    "apellido": "Pérez",
    "correo": "juan@example.com",
    "rol": "transportista",
    "rol_anterior": "cliente"
  }
}
```
**Notas:**
- Solo admins pueden cambiar roles
- Al cambiar a transportista: crea registro automático en transportistas
- Al cambiar de transportista: actualiza estado a "No Disponible"
- Roles válidos: `cliente`, `transportista`, `admin`

---

## 🚛 TRANSPORTISTAS ENDPOINTS

### GET `/transportistas`
**Descripción:** Obtener todos los transportistas
**Respuesta:**
```json
[
  {
    "id": 1,
    "ci": "12345678",
    "telefono": "555-1234",
    "estado": "Disponible",
    "fecha_registro": "2024-01-15T10:30:00Z",
    "usuario": {
      "id": 2,
      "nombre": "Carlos",
      "apellido": "López",
      "correo": "carlos@example.com"
    }
  }
]
```

### GET `/transportistas/{id}`
**Descripción:** Obtener transportista por ID

### POST `/transportistas`
**Descripción:** Crear transportista
```json
{
  "id_usuario": 2,
  "ci": "12345678",
  "telefono": "555-1234",
  "estado": "Disponible"
}
```

### PUT `/transportistas/{id}`
**Descripción:** Actualizar transportista

### DELETE `/transportistas/{id}`
**Descripción:** Eliminar transportista

### POST `/transportistas/completo`
**Descripción:** Crear transportista completo (usuario + transportista)
```json
{
  "nombre": "Carlos",
  "apellido": "López",
  "correo": "carlos@example.com",
  "contrasena": "password123",
  "ci": "12345678",
  "telefono": "555-1234"
}
```

### GET `/transportistas/estado/{estado}`
**Descripción:** Obtener transportistas por estado

### GET `/transportistas/disponibles`
**Descripción:** Obtener transportistas disponibles

---

## 📦 ENVÍOS ENDPOINTS

### POST `/envios/completo`
**Descripción:** Crear envío completo (cliente)
```json
{
  "id_direccion": 1,
  "particiones": [
    {
      "cargas": [
        {
          "tipo": "Frutas",
          "variedad": "Manzanas",
          "cantidad": 100,
          "empaquetado": "Cajas",
          "peso": 50.5
        }
      ],
      "recogidaEntrega": {
        "fecha_recogida": "2024-01-20",
        "hora_recogida": "08:00:00",
        "hora_entrega": "16:00:00",
        "instrucciones_recogida": "Entregar en recepción",
        "instrucciones_entrega": "Confirmar con cliente"
      },
      "id_tipo_transporte": 1
    }
  ]
}
```

### POST `/envios/completo-admin`
**Descripción:** Crear envío completo (admin)
```json
{
  "id_usuario_cliente": 1,
  "ubicacion": {
    "nombreorigen": "Mercado Central",
    "origen_lng": -74.0059,
    "origen_lat": 40.7128,
    "nombredestino": "Supermercado ABC",
    "destino_lng": -74.0060,
    "destino_lat": 40.7129,
    "rutageojson": "{\"type\":\"FeatureCollection\",\"features\":[]}"
  },
  "particiones": [
    {
      "cargas": [
        {
          "tipo": "Frutas",
          "variedad": "Manzanas",
          "cantidad": 100,
          "empaquetado": "Cajas",
          "peso": 50.5
        }
      ],
      "recogidaEntrega": {
        "fecha_recogida": "2024-01-20",
        "hora_recogida": "08:00:00",
        "hora_entrega": "16:00:00",
        "instrucciones_recogida": "Entregar en recepción",
        "instrucciones_entrega": "Confirmar con cliente"
      },
      "id_tipo_transporte": 1,
      "id_transportista": 2,    
      "id_vehiculo": 3           
    }
  ]
}
```

### GET `/envios`
**Descripción:** Obtener todos los envíos
**Respuesta:**
```json
[
  {
    "id": 1,
    "id_usuario": 1,
    "estado": "En curso",
    "fecha_creacion": "2024-01-15T10:30:00Z",
    "fecha_inicio": "2024-01-16T08:00:00Z",
    "fecha_entrega": null,
    "id_direccion": 1,
    "usuario": {
      "id": 1,
      "nombre": "Juan",
      "apellido": "Pérez"
    },
    "direccion": {
      "id": 1,
      "nombreorigen": "Mercado Central",
      "nombredestino": "Supermercado ABC"
    },
    "particiones": [
      {
        "id_asignacion": 1,
        "estado": "En curso",
        "fecha_asignacion": "2024-01-15T11:00:00Z",
        "fecha_inicio": "2024-01-16T08:00:00Z",
        "fecha_fin": null,
        "transportista": {
          "nombre": "Carlos",
          "apellido": "López",
          "ci": "12345678",
          "telefono": "555-1234"
        },
        "vehiculo": {
          "placa": "ABC-123",
          "tipo": "Camión"
        },
        "cargas": [
          {
            "id": 1,
            "tipo": "Frutas",
            "variedad": "Manzanas",
            "cantidad": 100,
            "empaquetado": "Cajas",
            "peso": 50.5
          }
        ],
        "recogidaEntrega": {
          "fecha_recogida": "2024-01-20",
          "hora_recogida": "08:00:00",
          "hora_entrega": "16:00:00",
          "instrucciones_recogida": "Entregar en recepción",
          "instrucciones_entrega": "Confirmar con cliente"
        },
        "tipoTransporte": {
          "nombre": "Refrigerado",
          "descripcion": "Transporte con temperatura controlada"
        }
      }
    ]
  }
]
```

### GET `/envios/{id}`
**Descripción:** Obtener envío por ID

### GET `/envios/mis-envios`
**Descripción:** Obtener mis envíos (usuario autenticado)

### PUT `/envios/{id_envio}/asignar`
**Descripción:** Asignar transportista y vehículo (método original)
```json
{
  "id_transportista": 1,
  "id_vehiculo": 1,
  "carga": {
    "tipo": "Frutas",
    "variedad": "Manzanas",
    "cantidad": 100,
    "empaquetado": "Cajas",
    "peso": 50.5
  },
  "recogidaEntrega": {
    "fecha_recogida": "2024-01-20",
    "hora_recogida": "08:00:00",
    "hora_entrega": "16:00:00",
    "instrucciones_recogida": "Entregar en recepción",
    "instrucciones_entrega": "Confirmar con cliente"
  },
  "id_tipo_transporte": 1
}
```

### PUT `/envios/asignacion/{id_asignacion}/asignar`
**Descripción:** Asignar transportista y vehículo a partición existente
```json
{
  "id_transportista": 1,
  "id_vehiculo": 1
}
```

### POST `/envios/asignacion/{id_asignacion}/iniciar`
**Descripción:** Iniciar viaje (transportista)
**Respuesta:**
```json
{
  "mensaje": "Viaje iniciado correctamente para esta asignación",
  "id_asignacion": 1,
  "token": "550e8400-e29b-41d4-a716-446655440000",
  "imagenQR": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==",
  "fecha_creacion": "2024-01-16T08:00:00Z"
}
```

### POST `/envios/asignacion/{id_asignacion}/finalizar`
**Descripción:** Finalizar envío (transportista)

### GET `/envios/transportista/asignados`
**Descripción:** Obtener envíos asignados al transportista autenticado

### POST `/envios/asignacion/{id_asignacion}/checklist-condiciones`
**Descripción:** Registrar checklist de condiciones
```json
{
  "temperatura_controlada": true,
  "embalaje_adecuado": true,
  "carga_segura": true,
  "vehiculo_limpio": true,
  "documentos_presentes": true,
  "ruta_conocida": true,
  "combustible_completo": true,
  "gps_operativo": true,
  "comunicacion_funcional": true,
  "estado_general_aceptable": true,
  "observaciones": "Todo en orden"
}
```

### POST `/envios/asignacion/{id_asignacion}/checklist-incidentes`
**Descripción:** Registrar checklist de incidentes
```json
{
  "retraso": false,
  "problema_mecanico": false,
  "accidente": false,
  "perdida_carga": false,
  "condiciones_climaticas_adversas": false,
  "ruta_alternativa_usada": false,
  "contacto_cliente_dificultoso": false,
  "parada_imprevista": false,
  "problemas_documentacion": false,
  "otros_incidentes": false,
  "descripcion_incidente": "Viaje sin incidentes"
}
```

### GET `/envios/{id_envio}/documento`
**Descripción:** Generar documento de envío completo

### GET `/envios/asignacion/{id_asignacion}/documento`
**Descripción:** Generar documento de partición específica

### GET `/envios/particiones/en-curso`
**Descripción:** Obtener particiones en curso del cliente

### PUT `/envios/{id_envio}/estado-global`
**Descripción:** Actualizar estado global del envío (solo admin)

---

## ✍️ FIRMAS ENDPOINTS

### POST `/firmas/envio/{id_asignacion}`
**Descripción:** Guardar firma de envío
```json
{
  "imagenFirma": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=="
}
```

### POST `/firmas/transportista/{id_asignacion}`
**Descripción:** Guardar firma de transportista
```json
{
  "imagenFirma": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=="
}
```

### GET `/firmas/envio/{id_asignacion}`
**Descripción:** Obtener firma de envío
**Respuesta:**
```json
{
  "id_asignacion": 1,
  "imagenFirma": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==",
  "fechaFirma": "2024-01-16T10:30:00Z"
}
```

### GET `/firmas/transportista/{id_asignacion}`
**Descripción:** Obtener firma de transportista

### GET `/firmas/transportista/asignacion/{id_asignacion}`
**Descripción:** Obtener firma por asignación (método específico)

### PUT `/firmas/envio/{id_asignacion}`
**Descripción:** Actualizar firma de envío

### DELETE `/firmas/envio/{id_asignacion}`
**Descripción:** Eliminar firma de envío

---

## 📱 QR TOKENS ENDPOINTS

### POST `/qr/generar/{id_asignacion}`
**Descripción:** Generar QR token
**Respuesta:**
```json
{
  "mensaje": "QR token generado correctamente",
  "id_asignacion": 1,
  "token": "550e8400-e29b-41d4-a716-446655440000",
  "imagenQR": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==",
  "fecha_creacion": "2024-01-16T08:00:00Z",
  "fecha_expiracion": "2024-01-17T08:00:00Z"
}
```

### GET `/qr/{id_asignacion}`
**Descripción:** Obtener QR token por asignación

### GET `/qr/transportista/{id_asignacion}`
**Descripción:** Obtener QR específico para transportista
**Respuesta:**
```json
{
  "mensaje": "QR encontrado correctamente",
  "id_asignacion": 1,
  "token": "550e8400-e29b-41d4-a716-446655440000",
  "imagenQR": "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==",
  "usado": false,
  "fecha_creacion": "2024-01-16T08:00:00Z",
  "fecha_expiracion": "2024-01-17T08:00:00Z",
  "frontend_url": "https://orgtrackprueba.netlify.app/validar-qr/550e8400-e29b-41d4-a716-446655440000"
}
```

### POST `/qr/validar`
**Descripción:** Validar QR token
```json
{
  "token": "550e8400-e29b-41d4-a716-446655440000"
}
```
**Respuesta:**
```json
{
  "mensaje": "Token QR válido",
  "valido": true,
  "asignacion": {
    "id_asignacion": 1,
    "estado": "En curso",
    "cliente": {
      "nombre": "Juan",
      "apellido": "Pérez"
    },
    "origen": "Mercado Central",
    "destino": "Supermercado ABC",
    "vehiculo": {
      "placa": "ABC-123",
      "tipo": "Camión"
    },
    "transportista": {
      "nombre": "Carlos",
      "apellido": "López"
    }
  }
}
```

### GET `/qr/cliente/tokens`
**Descripción:** Obtener QR tokens del cliente autenticado

### DELETE `/qr/{id_asignacion}`
**Descripción:** Eliminar QR token

---

## 🚗 VEHÍCULOS ENDPOINTS

### GET `/vehiculos`
**Descripción:** Obtener todos los vehículos

### GET `/vehiculos/{id}`
**Descripción:** Obtener vehículo por ID

### POST `/vehiculos`
**Descripción:** Crear vehículo
```json
{
  "placa": "ABC-123",
  "tipo": "Camión",
  "estado": "Disponible"
}
```

### PUT `/vehiculos/{id}`
**Descripción:** Actualizar vehículo

### DELETE `/vehiculos/{id}`
**Descripción:** Eliminar vehículo

---

## 📍 UBICACIONES ENDPOINTS

### GET `/ubicaciones`
**Descripción:** Obtener todas las ubicaciones

### GET `/ubicaciones/{id}`
**Descripción:** Obtener ubicación por ID

### POST `/ubicaciones`
**Descripción:** Crear ubicación
```json
{
  "id_usuario": 1,
  "nombreorigen": "Mercado Central",
  "origen_lng": -74.0059,
  "origen_lat": 40.7128,
  "nombredestino": "Supermercado ABC",
  "destino_lng": -74.0060,
  "destino_lat": 40.7129,
  "rutageojson": "{\"type\":\"FeatureCollection\",\"features\":[]}"
}
```

### PUT `/ubicaciones/{id}`
**Descripción:** Actualizar ubicación

### DELETE `/ubicaciones/{id}`
**Descripción:** Eliminar ubicación

---

## 🚚 TIPO TRANSPORTE ENDPOINTS

### GET `/tipotransporte`
**Descripción:** Obtener todos los tipos de transporte

---

## 📊 CÓDIGOS DE RESPUESTA HTTP

- **200** - OK (Éxito)
- **201** - Created (Creado exitosamente)
- **400** - Bad Request (Datos inválidos)
- **401** - Unauthorized (No autenticado)
- **403** - Forbidden (Sin permisos)
- **404** - Not Found (No encontrado)
- **422** - Unprocessable Entity (Error de validación)
- **500** - Internal Server Error (Error interno)

---

## 🔒 AUTENTICACIÓN

Todas las rutas (excepto `/auth/*`) requieren el header:
```
Authorization: Bearer {token}
```

---

## 📝 NOTAS IMPORTANTES

1. **Base64 Images:** Las firmas se envían como strings base64
2. **Fechas:** Formato ISO 8601 (YYYY-MM-DDTHH:mm:ssZ)
3. **IDs:** Todos los IDs son enteros
4. **Estados:** Los estados son strings predefinidos
5. **Validaciones:** Laravel valida automáticamente los datos
6. **Transacciones:** Operaciones complejas usan transacciones de base de datos
7. **Logging:** Todos los errores se registran en logs
8. **Relaciones:** Los datos incluyen relaciones automáticamente

---

## 🚀 EJEMPLO DE USO COMPLETO

### 1. Registrar usuario
```bash
POST /api/auth/register
```

### 2. Iniciar sesión
```bash
POST /api/auth/login
```

### 3. Crear envío
```bash
POST /api/envios/completo
Authorization: Bearer {token}
```

### 4. Asignar transportista
```bash
PUT /api/envios/asignacion/{id}/asignar
Authorization: Bearer {token}
```

### 5. Iniciar viaje
```bash
POST /api/envios/asignacion/{id}/iniciar
Authorization: Bearer {token}
```

### 6. Finalizar envío
```bash
POST /api/envios/asignacion/{id}/finalizar
Authorization: Bearer {token}
```

### 7. Cambiar rol de usuario (admin)
```bash
PUT /api/usuarios/{id}/cambiar-rol
Authorization: Bearer {token}
Content-Type: application/json

{
  "rol": "transportista"
}
```

---

**¡API completamente funcional con PostgreSQL y Laravel!** 🎉
