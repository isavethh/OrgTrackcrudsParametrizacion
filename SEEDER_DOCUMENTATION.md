# Documentación del DatosSeeder

## 📋 Resumen de Cambios Implementados

### ✅ Correcciones Aplicadas

1. **Generación de Códigos de Acceso**
   - Todos los envíos con transportista y vehículo asignados ahora generan `codigo_acceso` (6 caracteres)
   - Se genera usando: `strtoupper(substr(md5(uniqid(rand(), true)), 0, 6))`
   - Únicamente los envíos sin asignar (Pendiente puro) no tienen código

2. **Estados de Envío Correctos**
   - **Pendiente**: Envío creado pero sin transportista/vehículo
   - **Asignado**: Envío con transportista/vehículo pero aún no iniciado
   - **En curso**: Transportista ya recogió la carga y está en camino
   - **Entregado**: Envío completado con firmas

3. **Estados de Recursos (Transportista/Vehículo)**
   - Cuando se asigna un transportista/vehículo → estado cambia a **"No Disponible"**
   - Cuando se completa el envío → estado vuelve a **"Disponible"**
   - Esto previene dobles asignaciones

4. **Rutas Geográficas Realistas**
   - Todas las rutas usan coordenadas reales de Bolivia
   - Incluyen puntos intermedios para simular trayectos reales
   - Se generan segmentos de ruta con propiedades (tramo, distancia_km)
   - Formato GeoJSON para compatibilidad con mapas

---

## 🚚 Envíos Creados (5 escenarios completos)

### Envío 1: ENTREGADO ✅
- **Ruta**: Finca El Paraíso (Warnes, SCZ) → Planta OrganiCo (La Paz)
- **Producto**: 150 cajas de Manzanas (1500 kg)
- **Transportista**: Juan Pérez (ID: 1) - **DISPONIBLE** ✅
- **Vehículo**: Camión SCZ-1234 (ID: 1) - **DISPONIBLE** ✅
- **Tipo Transporte**: Refrigerado
- **Código Acceso**: ✅ Generado
- **Documentos**: ✅ Checklist, Firmas (transportista + cliente), QR usado
- **Distancia**: ~954 km (Warnes → Cbba → Oruro → La Paz)
- **Estado Actual**: Completado hace 7 días

### Envío 2: EN CURSO 🚛
- **Ruta**: Cooperativa Andina (Challapata, ORU) → Planta OrganiCo (La Paz)
- **Producto**: 80 sacos de Quinua (2000 kg)
- **Transportista**: Roberto Vargas (ID: 2) - **NO DISPONIBLE** 🔒
- **Vehículo**: Camión LPZ-5678 (ID: 2) - **NO DISPONIBLE** 🔒
- **Tipo Transporte**: Isotérmico
- **Código Acceso**: ✅ Generado
- **Documentos**: ✅ Firma transportista, QR activo
- **Distancia**: ~350 km (Challapata → Oruro → La Paz)
- **Estado Actual**: En tránsito (iniciado hace 2 días)

### Envío 3: ASIGNADO 📦
- **Ruta**: Cultivos Verde Vida (Quillacollo, CBBA) → Planta OrganiCo (La Paz)
- **Producto**: 200 kg de Lechugas hidropónicas
- **Transportista**: Miguel Torres (ID: 3) - **NO DISPONIBLE** 🔒
- **Vehículo**: Camioneta CBB-9012 (ID: 3) - **NO DISPONIBLE** 🔒
- **Tipo Transporte**: Refrigerado (2-4°C)
- **Código Acceso**: ✅ Generado
- **Documentos**: QR activo
- **Distancia**: ~434 km (Cochabamba → Oruro → La Paz)
- **Estado Actual**: Asignado pero no iniciado (programado para mañana)

### Envío 4: PENDIENTE ⏳
- **Ruta**: Finca Citrus del Valle (Tarija) → Centro Distribución Andes (Cochabamba)
- **Producto**: 100 cajas de Naranjas (1500 kg)
- **Transportista**: Sin asignar
- **Vehículo**: Sin asignar
- **Tipo Transporte**: Refrigerado
- **Código Acceso**: ❌ No generado (se generará al asignar)
- **Documentos**: Ninguno
- **Distancia**: ~790 km (Tarija → Potosí → Cochabamba)
- **Estado Actual**: Esperando asignación de transportista

### Envío 5: ASIGNADO - Carga Mixta (Multitemperatura) 🌡️
- **Ruta**: Productora Amazónica (Trinidad, BENI) → Mercado Mayorista (La Paz)
- **Productos**: 
  - 80 cajas de Manzanas (800 kg)
  - 120 cajas de Tomates (960 kg)
- **Transportista**: Juan Pérez (ID: 1) - **NO DISPONIBLE** 🔒 (nuevo viaje)
- **Vehículo**: Camioneta TJA-3456 (ID: 4) - **NO DISPONIBLE** 🔒
- **Tipo Transporte**: Multitemperatura
- **Código Acceso**: ✅ Generado
- **Documentos**: QR activo
- **Distancia**: ~725 km (Trinidad → Rurrenabaque → Yungas → La Paz)
- **Estado Actual**: Asignado (programado para hoy a las 4:00 AM)
- **Nota**: Ruta de montaña difícil, requiere vehículo en óptimas condiciones

---

## 🗺️ Rutas Implementadas

Todas las rutas usan coordenadas GPS reales de Bolivia:

1. **Warnes (SCZ) → La Paz**: Ruta troncal Este-Oeste
2. **Challapata (ORU) → La Paz**: Ruta Altiplano Sur
3. **Cochabamba → La Paz**: Ruta más transitada del país
4. **Tarija → Cochabamba**: Ruta Sur-Centro vía Potosí
5. **Trinidad (BENI) → La Paz**: Ruta Amazonía-Altiplano (Los Yungas)

Cada ruta incluye:
- ✅ Coordenadas de origen y destino
- ✅ Puntos intermedios (ciudades de paso)
- ✅ Segmentos con propiedades (tramo, distancia)
- ✅ Formato GeoJSON válido para mapas

---

## 👥 Recursos Disponibles

### Transportistas (3)
| ID | Nombre | Email | Estado Actual |
|----|--------|-------|---------------|
| 1 | Juan Pérez | juan@transporte.com | 🔒 No Disponible (Envío 5) |
| 2 | Roberto Vargas | roberto@transporte.com | 🔒 No Disponible (Envío 2) |
| 3 | Miguel Torres | miguel@transporte.com | 🔒 No Disponible (Envío 3) |

### Vehículos (5)
| ID | Placa | Tipo | Capacidad | Estado Actual |
|----|-------|------|-----------|---------------|
| 1 | SCZ-1234 | Camión Refrigerado | 5000 kg | ✅ Disponible |
| 2 | LPZ-5678 | Camión Estándar | 4500 kg | 🔒 No Disponible (Envío 2) |
| 3 | CBB-9012 | Camioneta Refrigerada | 2000 kg | 🔒 No Disponible (Envío 3) |
| 4 | TJA-3456 | Camioneta Estándar | 1800 kg | 🔒 No Disponible (Envío 5) |
| 5 | ORU-7890 | Furgoneta Refrigerada | 1000 kg | ✅ Disponible |

---

## 🔑 Lógica de Códigos de Acceso

### ¿Cuándo se genera?
```
SI (envío tiene transportista Y vehículo asignados)
    ENTONCES generar codigo_acceso
SINO
    codigo_acceso = null
```

### Formato
- **Longitud**: 6 caracteres
- **Formato**: Alfanumérico mayúsculas
- **Ejemplo**: `A3F7D2`, `9B4E1C`
- **Único**: Cada asignación tiene un código diferente

### Casos de Uso
1. **Envío Pendiente** (sin asignar) → ❌ Sin código
2. **Envío Asignado** (con transportista/vehículo) → ✅ Con código
3. **Envío En Curso** → ✅ Con código (ya generado)
4. **Envío Entregado** → ✅ Con código (usado)

---

## 📊 Resumen de Estados

### Estados de Envío
| Estado | Cantidad | Descripción |
|--------|----------|-------------|
| Pendiente | 1 | Sin asignar recursos |
| Asignado | 2 | Con recursos pero no iniciado |
| En curso | 1 | Transportista en camino |
| Entregado | 1 | Completado con firmas |

### Estados de Transportista/Vehículo
| Estado | Transportistas | Vehículos | Descripción |
|--------|----------------|-----------|-------------|
| Disponible | 0 | 2 | Libres para asignar |
| No Disponible | 3 | 3 | Asignados a envíos activos |

---

## 🎯 Validaciones Implementadas

1. ✅ **Código de acceso** solo en asignaciones con recursos
2. ✅ **Estados coherentes** entre envío y asignación
3. ✅ **Recursos marcados** como No Disponible al asignar
4. ✅ **Recursos liberados** al completar envío
5. ✅ **Rutas con coordenadas reales** y segmentos
6. ✅ **Historial de estados** completo para cada envío
7. ✅ **QR tokens** con estados correctos (Activo/Usado)

---

## 🚀 Cómo Ejecutar el Seeder

```bash
php artisan db:seed --class=DatosSeeder
```

### Prerequisitos
- Base de datos configurada
- Migraciones ejecutadas
- Estados base creados (Pendiente, Asignado, En curso, etc.)

---

## 📝 Notas Adicionales

- **Envío 1** simula un ciclo completo: asignación → transporte → entrega → liberación de recursos
- **Envío 5** demuestra carga mixta en un solo vehículo multitemperatura
- **Todas las rutas** son navegables en Google Maps para verificación
- **Los códigos de acceso** son únicos y se pueden usar para autenticación de transportistas
- **Los timestamps** están escalonados para simular un sistema en operación real

---

## 🔍 Para Visualización en Mapa

Cada dirección incluye `rutageojson` en formato GeoJSON LineString:

```json
{
  "type": "LineString",
  "coordinates": [
    [-63.1812, -17.7833],  // Origen
    [-63.1500, -17.7500],  // Punto intermedio
    ...
    [-68.1193, -16.5000]   // Destino
  ]
}
```

Puedes usar bibliotecas como:
- Leaflet.js
- Google Maps API
- Mapbox
- OpenLayers

Para renderizar las rutas en el mapa del sistema.
