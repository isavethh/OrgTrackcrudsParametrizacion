# Restricción de Edición de Envíos en AgroNexus

## 🎯 Objetivo

Implementar restricciones para que los usuarios de AgroNexus solo puedan **editar envíos que estén pendientes de aprobación**. Una vez que OrgTrack aprueba o rechaza un envío, este **solo puede ser visualizado**.

---

## ✅ Cambios Implementados

### 1. **Vista Index - Lista de Envíos** ✓

**Archivo**: `AgroNexus/resources/views/envios/index.blade.php`

#### Cambios:
- **Nueva columna "Estado"** que muestra el estado de aprobación:
  - 🟡 **Pendiente**: Esperando decisión de OrgTrack
  - 🟢 **Aprobado**: Aprobado por OrgTrack
  - 🔴 **Rechazado**: Rechazado por OrgTrack

- **Botón de Editar Condicional**:
  - ✅ **Visible**: Solo si `estado_aprobacion = 'pendiente'`
  - 🔒 **Bloqueado**: Si ya fue aprobado o rechazado (muestra candado)

```php
@if($estadoAprobacion == 'pendiente')
    <a href="{{ route('envios.edit', $envio['id']) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i>
    </a>
@else
    <button class="btn btn-secondary" disabled>
        <i class="fas fa-lock"></i>
    </button>
@endif
```

---

### 2. **Vista Show - Detalle del Envío** ✓

**Archivo**: `AgroNexus/resources/views/envios/show.blade.php`

#### Cambios:

**Badge de Estado en Header**:
- Muestra el estado de aprobación en la parte superior
- Color y texto según el estado

**Alertas Informativas**:

1. **Si está RECHAZADO**:
```html
<div class="alert alert-danger">
    <h5>Envío Rechazado por OrgTrack</h5>
    <p>Motivo: [motivo del rechazo]</p>
</div>
```

2. **Si está APROBADO**:
```html
<div class="alert alert-success">
    Este envío ha sido aprobado por OrgTrack
    y asignado a [nombre del transportista]
</div>
```

3. **Si está PENDIENTE**:
```html
<div class="alert alert-info">
    Este envío está en espera de aprobación.
    Puede editarlo mientras esté pendiente.
</div>
```

**Botones de Acción**:
- Botón "Editar": Solo visible si está pendiente
- Botón bloqueado: Si ya fue procesado

---

### 3. **Controlador - Validaciones Backend** ✓

**Archivo**: `AgroNexus/app/Http/Controllers/Web/EnvioController.php`

#### Método `edit()`:

Valida el estado antes de mostrar el formulario:

```php
public function edit(Envio $envio)
{
    // Consultar estado desde la API
    $response = Http::get("{$this->apiUrl}/envios/{$envio->envioid}");
    
    if ($response->successful()) {
        $estadoAprobacion = $response->json('data.estado_aprobacion');
        
        // Solo permitir editar si está pendiente
        if ($estadoAprobacion !== 'pendiente') {
            return redirect()->route('envios.show', $envio->envioid)
                ->with('error', "No se puede editar un envío que ya ha sido {$estadoAprobacion}.");
        }
    }
    
    // Continuar con el flujo normal...
}
```

#### Método `update()`:

Valida el estado antes de actualizar:

```php
public function update(Request $request, Envio $envio)
{
    // Verificar estado desde la API
    $response = Http::get("{$this->apiUrl}/envios/{$envio->envioid}");
    
    if ($response->successful()) {
        $estadoAprobacion = $response->json('data.estado_aprobacion');
        
        // Solo permitir actualizar si está pendiente
        if ($estadoAprobacion !== 'pendiente') {
            return redirect()->route('envios.show', $envio->envioid)
                ->with('error', "No se puede modificar un envío que ya ha sido {$estadoAprobacion}.");
        }
    }
    
    // Continuar con la actualización...
}
```

**⚠️ Importante**: Las validaciones consultan el estado desde la API para asegurar que siempre se tenga el estado más actualizado.

---

## 🔄 Flujo de Estados

```
┌─────────────────────────────────────────────────────────────┐
│                    ENVÍO PENDIENTE                           │
│                                                              │
│  ✅ Puede EDITAR                                            │
│  ✅ Puede VER                                               │
│  ✅ Botones habilitados                                     │
│  ✅ Formulario accesible                                    │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       │ OrgTrack DECIDE
                       │
          ┌────────────┴────────────┐
          │                         │
      APROBAR                   RECHAZAR
          │                         │
          ▼                         ▼
┌──────────────────┐      ┌──────────────────┐
│ ENVÍO APROBADO   │      │ ENVÍO RECHAZADO  │
│                  │      │                  │
│ ❌ NO puede editar│      │ ❌ NO puede editar│
│ ✅ Solo VER       │      │ ✅ Solo VER       │
│ 🔒 Botón bloqueado│      │ 🔒 Botón bloqueado│
│ ⚠️ Redirige al ver│      │ ⚠️ Redirige al ver│
└──────────────────┘      └──────────────────┘
```

---

## 📊 Matriz de Permisos

| Estado      | Ver | Editar | Actualizar | Botón Edit | Color Badge |
|-------------|-----|--------|------------|------------|-------------|
| `pendiente` | ✅  | ✅     | ✅         | ⚠️ Amarillo | 🟡 Amarillo |
| `aprobado`  | ✅  | ❌     | ❌         | 🔒 Gris    | 🟢 Verde    |
| `rechazado` | ✅  | ❌     | ❌         | 🔒 Gris    | 🔴 Rojo     |

---

## 🛡️ Niveles de Protección

### 1. **Nivel Visual (Frontend)**
- Oculta/deshabilita botones de edición
- Muestra alertas informativas
- Previene acciones no permitidas en la UI

### 2. **Nivel Lógico (Backend)**
- Valida el estado antes de mostrar formulario
- Valida el estado antes de guardar cambios
- Consulta siempre la API para estado actualizado

### 3. **Nivel de Datos (API)**
- El estado se almacena centralizadamente
- Solo OrgTrack puede cambiar el estado
- AgroNexus solo consulta, no modifica

---

## 🎨 Experiencia de Usuario

### Caso 1: Usuario intenta editar envío aprobado

1. En la lista, ve el botón de editar bloqueado 🔒
2. Si intenta acceder directamente a la URL de edición:
   - Es redirigido a la vista de detalle
   - Ve un mensaje: *"No se puede editar un envío que ya ha sido aprobado"*
   - Ve una alerta verde: *"Este envío ha sido aprobado por OrgTrack"*

### Caso 2: Usuario intenta editar envío rechazado

1. En la lista, ve el botón de editar bloqueado 🔒
2. Si intenta acceder directamente a la URL:
   - Es redirigido a la vista de detalle
   - Ve un mensaje: *"No se puede editar un envío que ya ha sido rechazado"*
   - Ve una alerta roja con el motivo del rechazo

### Caso 3: Usuario edita envío pendiente

1. En la lista, ve el botón de editar activo ⚠️
2. Puede hacer clic y editar normalmente
3. Ve una alerta azul: *"Este envío está en espera de aprobación"*
4. Puede guardar cambios sin restricciones

---

## 🧪 Cómo Probar

### Prueba 1: Editar Envío Pendiente

1. Crear un envío nuevo desde AgroNexus
2. En la lista, verificar que aparece con badge amarillo "Pendiente"
3. Verificar que el botón de editar está activo
4. Hacer clic en editar
5. Verificar que el formulario se carga correctamente
6. Hacer cambios y guardar
7. ✅ Debe guardarse sin problemas

### Prueba 2: Intentar Editar Envío Aprobado

1. Ir a OrgTrack y aprobar un envío pendiente
2. Volver a AgroNexus
3. En la lista, verificar que el badge cambió a verde "Aprobado"
4. Verificar que el botón de editar está bloqueado (candado gris)
5. Copiar la URL de edición manualmente: `/envios/{id}/edit`
6. Pegar en el navegador e intentar acceder
7. ✅ Debe redirigir al show con mensaje de error

### Prueba 3: Intentar Editar Envío Rechazado

1. Ir a OrgTrack y rechazar un envío pendiente
2. Volver a AgroNexus
3. En la lista, verificar que el badge cambió a rojo "Rechazado"
4. Verificar que el botón de editar está bloqueado
5. Hacer clic en "Ver" para abrir el detalle
6. Verificar que se muestra la alerta roja con el motivo del rechazo
7. Verificar que no hay botón de editar
8. ✅ Solo debe estar disponible el botón "Volver"

---

## 📝 Archivos Modificados

### AgroNexus

1. **resources/views/envios/index.blade.php**
   - Nueva columna "Estado"
   - Botón de editar condicional
   - Badge de estado con colores

2. **resources/views/envios/show.blade.php**
   - Badge de estado en header
   - Alertas informativas según estado
   - Botón de editar condicional
   - Mostrar motivo de rechazo

3. **app/Http/Controllers/Web/EnvioController.php**
   - Validación en método `edit()`
   - Validación en método `update()`
   - Consulta de estado desde API

---

## ⚠️ Consideraciones Importantes

### 1. Sincronización
- El estado siempre se consulta desde la API
- Esto asegura que el usuario vea el estado más actualizado
- Previene condiciones de carrera

### 2. Seguridad
- Doble validación (UI + Backend)
- No se puede bypassear con URL directa
- Mensajes de error claros

### 3. UX/UI
- Feedback visual claro (colores y iconos)
- Mensajes descriptivos
- Botones deshabilitados en lugar de ocultos (mejor para accesibilidad)

### 4. Performance
- Consulta a la API solo cuando es necesario
- No impacta listado (estado ya viene en los datos)
- Solo consulta adicional al intentar editar

---

## 🚀 Mejoras Futuras Sugeridas

1. **Cache de Estado**: Cachear el estado por unos minutos para reducir llamadas a la API
2. **Notificación en Tiempo Real**: WebSockets para notificar cambios de estado
3. **Historial de Cambios**: Mostrar cuándo cambió de estado y quién lo aprobó/rechazó
4. **Solicitud de Cambio**: Permitir "solicitar cambios" en envíos aprobados con aprobación de OrgTrack

---

## 📞 Resumen Técnico

| Aspecto | Implementación |
|---------|---------------|
| **Consulta de Estado** | HTTP GET a `/api/envios/{id}` |
| **Campo Validado** | `estado_aprobacion` |
| **Valores Posibles** | `pendiente`, `aprobado`, `rechazado` |
| **Permiso Edit** | Solo si `estado_aprobacion == 'pendiente'` |
| **Validación** | UI (blade) + Backend (controller) |
| **Mensaje Error** | "No se puede editar un envío que ya ha sido {estado}" |

---

Fecha: 30 de Noviembre, 2025

