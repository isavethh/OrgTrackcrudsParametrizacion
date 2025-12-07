# API Pública de Envíos - Documentación

## Descripción General

Sistema de envíos públicos que permite a **cualquier persona** crear y consultar envíos sin necesidad de autenticación (sin JWT). 

### Flujo Completo

1. **Usuario anónimo**: Crea envío público → recibe `token_consulta` único
2. **Usuario anónimo**: Consulta su envío con el token (sin login)
3. **Admin**: Ve TODOS los envíos (normales + públicos), puede asignar transportista/vehículo
4. **Transportista**: Recibe asignación, hace checklist de condiciones, inicia viaje, checklist de incidentes, firmas, finaliza viaje
5. **Sistema**: Genera documento PDF con toda la información y firmas

### 🔑 Diferencias vs Envíos Normales

| Característica | Envío Normal (Cliente) | Envío Público (Anónimo) |
|---------------|------------------------|-------------------------|
| **Autenticación** | Requiere JWT (login) | No requiere autenticación |
| **id_usuario** | Asociado a cliente | `NULL` |
| **Identificación** | Por usuario autenticado | Por `token_consulta` único |
| **Datos remitente** | Del perfil del usuario | Proporcionados en el request |
| **Visibilidad admin** | ✅ Sí | ✅ Sí |
| **Asignación transportista** | ✅ Sí | ✅ Sí |
| **Checklists/Firmas** | ✅ Sí | ✅ Sí |
| **Documento PDF** | ✅ Sí | ✅ Sí |

---

## 📍 Endpoints Públicos (Sin JWT)

### 1. Crear Envío Público

Cualquier persona puede crear un envío sin autenticación.

**Endpoint:** `POST /api/public/envios`

**Headers:**
```
Content-Type: application/json
```

**Body (JSON):**
```json
{
  "nombre_remitente": "Juan Pérez",
  "telefono_remitente": "77123456",
  "email_remitente": "juan@example.com",
  "id_direccion": 5,
  "particiones": [
    {
      "id_tipo_transporte": 1,
      "cargas": [
        {
          "tipo": "Fruta",
          "variedad": "Manzana",
          "cantidad": 100,
          "peso": 500.5,
          "empaquetado": "Cajas de cartón"
        },
        {
          "tipo": "Verdura",
          "variedad": "Lechuga",
          "cantidad": 50,
          "peso": 200.0,
          "empaquetado": "Bolsas plásticas"
        }
      ],
      "recogidaEntrega": {
        "fecha_recogida": "2025-12-10",
        "hora_recogida": "08:00",
        "hora_entrega": "14:00",
        "instrucciones_recogida": "Tocar el timbre 3 veces",
        "instrucciones_entrega": "Dejar en la puerta trasera"
      }
    }
  ]
}
```

**Respuesta Exitosa (201):**
```json
{
  "mensaje": "Envío público creado exitosamente",
  "id_envio": 123,
  "token_consulta": "A1B2C3D4E5F6G7H8",
  "url_seguimiento": "http://localhost:8000/api/public/seguimiento/A1B2C3D4E5F6G7H8"
}
```

⚠️ **IMPORTANTE**: El `token_consulta` debe ser guardado por el usuario para consultar su envío después.

**Respuesta Error (400):**
```json
{
  "error": "La dirección no existe"
}
```

---

### 2. Consultar Envío Público

Cualquier persona con el token puede consultar el seguimiento completo del envío.

**Endpoint:** `GET /api/public/seguimiento/{token}`

**Ejemplo:** `GET /api/public/seguimiento/A1B2C3D4E5F6G7H8`

**Headers:**
```
No requiere headers especiales
```

**Respuesta Exitosa (200):**
```json
{
  "id_envio": 123,
  "nombre_remitente": "Juan Pérez",
  "telefono_remitente": "77123456",
  "email_remitente": "juan@example.com",
  "estado_envio": "En curso",
  "fecha_creacion": "2025-12-07 10:30:00",
  "fecha_inicio": "2025-12-10 08:15:00",
  "fecha_entrega": null,
  "nombre_origen": "Cochabamba Centro",
  "nombre_destino": "La Paz - Villa Fátima",
  "coordenadas_origen": {
    "lat": -17.3935,
    "lng": -66.1570
  },
  "coordenadas_destino": {
    "lat": -16.5000,
    "lng": -68.1500
  },
  "particiones": [
    {
      "id_asignacion": 456,
      "estado": "En curso",
      "codigo_acceso": "XYZ123",
      "fecha_inicio": "2025-12-10 08:15:00",
      "fecha_fin": null,
      "transportista": {
        "nombre": "Carlos",
        "apellido": "Rodríguez",
        "telefono": "70987654",
        "ci": "12345678"
      },
      "vehiculo": {
        "placa": "ABC-1234",
        "tipo": "Camión",
        "capacidad": 5000,
        "estado": "En uso"
      },
      "tipoTransporte": {
        "id": 1,
        "nombre": "Terrestre",
        "descripcion": "Transporte por carretera"
      },
      "recogidaEntrega": {
        "fecha_recogida": "2025-12-10",
        "hora_recogida": "08:00",
        "hora_entrega": "14:00",
        "instrucciones_recogida": "Tocar el timbre 3 veces",
        "instrucciones_entrega": "Dejar en la puerta trasera"
      },
      "cargas": [
        {
          "tipo": "Fruta",
          "variedad": "Manzana",
          "cantidad": 100,
          "peso": 500.5,
          "empaquetado": "Cajas de cartón"
        }
      ],
      "checklist_condiciones": {
        "id": 789,
        "fecha_registro": "2025-12-10 08:10:00",
        "detalles": [
          {
            "condicion": "Vehículo limpio",
            "cumple": true
          },
          {
            "condicion": "Documentos en regla",
            "cumple": true
          }
        ]
      },
      "checklist_incidentes": {
        "id": 790,
        "fecha_registro": "2025-12-10 14:05:00",
        "detalles": [
          {
            "tipo_incidente": "Retraso por tráfico",
            "ocurrio": true
          },
          {
            "tipo_incidente": "Daño en carga",
            "ocurrio": false
          }
        ]
      }
    }
  ]
}
```

**Respuesta Error (404):**
```json
{
  "error": "Token inválido o envío no encontrado"
}
```

---

## 📍 Endpoints Existentes (Con JWT)

Los siguientes endpoints **NO necesitan duplicarse**, funcionan igual con envíos públicos:

### Admin (requiere JWT + rol admin)

- ✅ `GET /api/envios` - Lista TODOS los envíos (normales + públicos)
- ✅ `GET /api/envios/{id}` - Detalle de cualquier envío
- ✅ `PUT /api/envios/asignacion/{id_asignacion}/asignar` - Asignar transportista/vehículo a envío público

### Transportista (requiere JWT + rol transportista)

- ✅ `GET /api/envios/transportista/asignados` - Lista envíos asignados (incluye públicos)
- ✅ `POST /api/envios/asignacion/{id_asignacion}/checklist-condiciones` - Checklist condiciones
- ✅ `POST /api/envios/asignacion/{id_asignacion}/iniciar` - Iniciar viaje
- ✅ `POST /api/envios/asignacion/{id_asignacion}/checklist-incidentes` - Checklist incidentes
- ✅ `POST /api/envios/asignacion/{id_asignacion}/finalizar` - Finalizar viaje (genera PDF)
- ✅ `GET /api/envios/asignacion/{id_asignacion}/documento` - Descargar documento PDF

---

## 🗄️ Cambios en Base de Datos

### Migración Ejecutada

```bash
php artisan migrate
```

### Nuevas Columnas en `envios`

| Columna | Tipo | Descripción |
|---------|------|-------------|
| `nombre_remitente` | VARCHAR(100) NULL | Nombre del remitente (para envíos públicos) |
| `telefono_remitente` | VARCHAR(20) NULL | Teléfono del remitente |
| `email_remitente` | VARCHAR(100) NULL | Email del remitente |
| `token_consulta` | VARCHAR(16) NULL UNIQUE | Token único para consultar el envío |
| `es_publico` | BOOLEAN DEFAULT false INDEX | Flag que indica si es envío público |

### Validaciones

- `id_usuario` ahora puede ser `NULL` (para envíos públicos)
- `token_consulta` es único en toda la tabla
- `es_publico` tiene índice para consultas rápidas

---

## 🧪 Ejemplo de Flujo Completo con cURL

### 1. Usuario Anónimo Crea Envío

```bash
curl -X POST http://localhost:8000/api/public/envios \
  -H "Content-Type: application/json" \
  -d '{
    "nombre_remitente": "Juan Pérez",
    "telefono_remitente": "77123456",
    "email_remitente": "juan@example.com",
    "id_direccion": 5,
    "particiones": [
      {
        "id_tipo_transporte": 1,
        "cargas": [
          {
            "tipo": "Fruta",
            "variedad": "Manzana",
            "cantidad": 100,
            "peso": 500.5,
            "empaquetado": "Cajas de cartón"
          }
        ],
        "recogidaEntrega": {
          "fecha_recogida": "2025-12-10",
          "hora_recogida": "08:00",
          "hora_entrega": "14:00"
        }
      }
    ]
  }'
```

**Respuesta:**
```json
{
  "mensaje": "Envío público creado exitosamente",
  "id_envio": 123,
  "token_consulta": "A1B2C3D4E5F6G7H8",
  "url_seguimiento": "http://localhost:8000/api/public/seguimiento/A1B2C3D4E5F6G7H8"
}
```

### 2. Usuario Consulta su Envío

```bash
curl -X GET http://localhost:8000/api/public/seguimiento/A1B2C3D4E5F6G7H8
```

### 3. Admin Asigna Transportista (requiere login)

```bash
# Primero login
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username": "admin", "password": "admin123"}'

# Asignar (usar el token JWT del login)
curl -X PUT http://localhost:8000/api/envios/asignacion/456/asignar \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {JWT_TOKEN}" \
  -d '{
    "id_transportista": 10,
    "id_vehiculo": 5
  }'
```

### 4. Transportista Inicia Viaje

```bash
# Login como transportista
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{"username": "transportista1", "password": "pass123"}'

# Iniciar viaje
curl -X POST http://localhost:8000/api/envios/asignacion/456/iniciar \
  -H "Content-Type: application/json" \
  -H "Authorization: Bearer {JWT_TRANSPORTISTA}"
```

---

## 🔐 Seguridad

### Endpoints Públicos
- **Sin autenticación**: Cualquiera puede crear y consultar con el token
- **Token único**: Cada envío tiene un token de 16 caracteres alfanuméricos (uppercase)
- **Solo consulta**: El endpoint público solo permite VER, no modificar

### Endpoints Protegidos
- **Admin**: Puede ver y asignar todos los envíos (normales + públicos)
- **Transportista**: Solo ve envíos asignados a él
- **Cliente normal**: Solo ve sus propios envíos

### Recomendaciones
- Guardar el `token_consulta` del usuario
- No compartir tokens públicamente (es como una contraseña)
- El token no caduca (persiste en BD)

---

## 📊 Vista Admin - Identificación de Envíos Públicos

En el frontend admin, los envíos públicos tienen:
- Campo `es_publico: true`
- Campo `nombre_remitente` en lugar de `usuario.nombre`
- Campo `id_usuario: null`

Ejemplo de cómo mostrarlos:

```javascript
envios.forEach(envio => {
  if (envio.es_publico) {
    console.log(`📦 Envío Público #${envio.id}`);
    console.log(`  👤 Remitente: ${envio.nombre_remitente}`);
    console.log(`  📞 Teléfono: ${envio.telefono_remitente}`);
  } else {
    console.log(`📦 Envío Cliente #${envio.id}`);
    console.log(`  👤 Cliente: ${envio.usuario.nombre} ${envio.usuario.apellido}`);
  }
});
```

---

## ✅ Checklist de Implementación

- [x] Migración de BD (agregar columnas)
- [x] Modelo `Envio` actualizado (fillable, casts)
- [x] Endpoint `POST /api/public/envios` (crear envío público)
- [x] Endpoint `GET /api/public/seguimiento/{token}` (consultar envío)
- [x] Modificado `obtenerTodos()` para incluir envíos públicos (admin)
- [x] Rutas públicas en `routes/api.php`
- [ ] Ejecutar migración: `php artisan migrate`
- [ ] Probar creación de envío público con Postman/cURL
- [ ] Probar consulta con token
- [ ] Verificar que admin ve envíos públicos
- [ ] Verificar asignación de transportista funciona
- [ ] Verificar checklists y firmas funcionan
- [ ] Verificar documento PDF se genera correctamente

---

## 🚀 Próximos Pasos

1. **Ejecutar migración**: 
   ```bash
   cd "e:\Proyecto de sistema lll\OrgTrack_web_Laravel"
   php artisan migrate
   ```

2. **Probar con Postman/Thunder Client**:
   - Crear envío público
   - Guardar el token retornado
   - Consultar con el token

3. **Verificar en Admin**:
   - Login como admin
   - Ver lista de envíos (debe incluir públicos)
   - Asignar transportista a envío público

4. **Flujo completo**:
   - Crear → Asignar → Iniciar → Finalizar → Documento PDF

---

## ❓ Preguntas Frecuentes

**P: ¿Necesito duplicar todos los endpoints?**
R: NO. Solo los 2 endpoints públicos (crear y consultar). El resto funciona igual.

**P: ¿El transportista puede ver envíos públicos?**
R: Sí, automáticamente cuando se le asignan.

**P: ¿Cómo recupero un token perdido?**
R: El token está en la BD (`envios.token_consulta`). Solo el admin puede consultarlo.

**P: ¿Puedo cambiar el token después?**
R: No está implementado, pero podrías agregar un endpoint admin para regenerarlo.

**P: ¿Los documentos PDF incluyen información del remitente público?**
R: Sí, el sistema usa `nombre_remitente` si `es_publico = true`.
