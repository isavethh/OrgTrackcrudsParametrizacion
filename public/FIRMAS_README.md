# 🏢 OrgTrack - Sistema de Firmas Digitales

## 📋 Descripción
Sistema web para que transportistas y clientes puedan firmar digitalmente usando los endpoints de la API de OrgTrack.

## 🚀 Características

### ✅ Funcionalidades Principales
- **Autenticación JWT**: Verificación de tokens de acceso
- **Firma Digital**: Canvas interactivo para firmar
- **Múltiples Roles**: Transportista y Cliente
- **Verificación**: Consultar firmas existentes
- **Responsive**: Funciona en móviles y desktop
- **Touch Support**: Soporte para pantallas táctiles

### 🎨 Interfaz
- **Diseño Moderno**: Gradientes y sombras
- **UX Intuitiva**: Fácil de usar
- **Alertas Visuales**: Feedback inmediato
- **Loading States**: Indicadores de carga

## 📱 Cómo Usar

### 1. **Acceder al Sistema**
```
http://localhost:8000/firmas.html
```

### 2. **Autenticación**
1. Ingresa tu **token JWT** en el campo correspondiente
2. Haz clic en **"Verificar Token"**
3. El sistema detectará tu rol automáticamente

### 3. **Firmar como Transportista**
1. Ingresa el **ID de asignación**
2. **Dibuja tu firma** en el canvas
3. Haz clic en **"Guardar Firma"**

### 4. **Firmar como Cliente**
1. Ingresa el **ID de asignación**
2. **Dibuja tu firma** en el canvas
3. Haz clic en **"Guardar Firma"**

### 5. **Verificar Firmas**
1. Ingresa el **ID de asignación**
2. Haz clic en **"Ver Firma Transportista"** o **"Ver Firma Cliente"**
3. Se mostrará la firma guardada

## 🔧 Endpoints Utilizados

### **Autenticación**
```bash
GET /api/usuarios
Authorization: Bearer {token}
```

### **Firma del Transportista**
```bash
POST /api/firmas/transportista/{id_asignacion}
Authorization: Bearer {token}
Content-Type: application/json

{
  "imagenFirma": "data:image/png;base64,..."
}
```

### **Firma del Cliente**
```bash
POST /api/firmas/envio/{id_asignacion}
Authorization: Bearer {token}
Content-Type: application/json

{
  "imagenFirma": "data:image/png;base64,..."
}
```

### **Verificar Firma Transportista**
```bash
GET /api/firmas/transportista/{id_asignacion}
Authorization: Bearer {token}
```

### **Verificar Firma Cliente**
```bash
GET /api/firmas/envio/{id_asignacion}
Authorization: Bearer {token}
```

## 🎯 Flujo de Trabajo

### **Para Transportistas:**
1. **Iniciar viaje** → Se genera QR automáticamente
2. **Firmar** → Usar este sistema para firmar
3. **Finalizar envío** → Completar el proceso

### **Para Clientes:**
1. **Recibir notificación** → QR generado por transportista
2. **Firmar** → Usar este sistema para firmar
3. **Confirmar entrega** → Proceso completado

## 📱 Compatibilidad

### **Navegadores Soportados:**
- ✅ Chrome 60+
- ✅ Firefox 55+
- ✅ Safari 12+
- ✅ Edge 79+

### **Dispositivos:**
- ✅ Desktop
- ✅ Tablet
- ✅ Móvil
- ✅ Touch screens

## 🔒 Seguridad

### **Autenticación:**
- **JWT Tokens**: Verificación de identidad
- **HTTPS**: Comunicación segura (en producción)
- **CORS**: Configuración de dominios permitidos

### **Datos:**
- **Base64**: Firmas codificadas
- **Validación**: Verificación de datos
- **Logs**: Registro de actividades

## 🛠️ Configuración

### **Variables de Entorno:**
```env
APP_URL=http://localhost:8000
FRONTEND_URL=https://orgtrackprueba.netlify.app
```

### **CORS (config/cors.php):**
```php
'allowed_origins' => [
    'http://localhost:8000',
    'https://orgtrackprueba.netlify.app'
]
```

## 🚨 Solución de Problemas

### **Error: "Token inválido"**
- Verifica que el token sea correcto
- Asegúrate de que no haya expirado
- Revisa el formato: `Bearer {token}`

### **Error: "No se puede guardar la firma"**
- Verifica que el ID de asignación sea correcto
- Asegúrate de que la asignación exista
- Revisa los permisos del usuario

### **Canvas no funciona**
- Verifica que el navegador soporte HTML5 Canvas
- Actualiza el navegador
- Habilita JavaScript

### **Firma muy pequeña**
- Usa un dispositivo con pantalla más grande
- Ajusta el zoom del navegador
- Usa un stylus para mayor precisión

## 📞 Soporte

### **Logs del Sistema:**
```bash
tail -f storage/logs/laravel.log
```

### **Debug de API:**
```bash
# Verificar endpoints
curl -H "Authorization: Bearer {token}" http://localhost:8000/api/usuarios
```

## 🔄 Actualizaciones

### **Versión 1.0**
- ✅ Sistema básico de firmas
- ✅ Autenticación JWT
- ✅ Soporte multi-rol
- ✅ Interfaz responsive

### **Próximas Versiones:**
- 🔄 Firma con certificado digital
- 🔄 Integración con blockchain
- 🔄 Notificaciones push
- 🔄 Historial de firmas

---

**¡Sistema listo para usar!** 🎉

