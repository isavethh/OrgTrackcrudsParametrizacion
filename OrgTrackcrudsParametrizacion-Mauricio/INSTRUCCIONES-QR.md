# 🚀 INSTRUCCIONES PARA USAR EL SISTEMA QR

## ⚠️ MUY IMPORTANTE

**EL SERVIDOR DEBE ESTAR CORRIENDO TODO EL TIEMPO**

### 📋 PASOS PARA INICIAR:

1. **Hacer doble clic en `INICIAR-SERVIDOR.bat`**
   - Se abrirá una ventana negra (terminal)
   - Dirá: "Server running on [http://127.0.0.1:8000]"
   - **NO CIERRES ESA VENTANA** mientras uses la aplicación

2. **Abrir el navegador y ir a:**
   ```
   http://localhost:8000
   ```

---

## 📱 CÓMO USAR LOS CÓDIGOS QR

### Generar QR:
1. Ve a **"Códigos QR"** en el menú lateral
2. Selecciona un cliente del dropdown
3. Click en **"Generar QR"** en el envío deseado
4. Aparecerá:
   - ✅ La imagen del código QR
   - 🔑 El código en un cuadro verde grande (ej: `ENV-KAHPQLMZ0J`)
   - 📄 Botón "Ver Documento"
   - 💾 Botón "Descargar QR"

### Escanear/Buscar QR:
1. Click en **"Leer Código QR"** (botón verde arriba)
2. Copia el código del cuadro verde (ej: `ENV-KAHPQLMZ0J`)
3. Pégalo en el campo "Código del Envío"
4. Click en **"Buscar Envío"**
5. Aparecerá el botón **"Ver Documento del Envío"**
6. Click para ver el PDF completo

### Ver Documento:
El PDF incluye:
- 📊 Información del cliente
- 🗺️ Origen y destino
- 📦 Todos los productos con detalles
- ⏱️ Cronología del envío
- 💰 Totales de peso y costo
- 📋 Historial de estados

---

## ❌ SI ALGO NO FUNCIONA

### Error: "El sitio no se encontraba disponible"
**Solución:** El servidor no está corriendo
- Haz doble clic en `INICIAR-SERVIDOR.bat`
- Espera a que diga "Server running on..."
- Recarga la página del navegador

### Error: Los QR no se descargan
**Solución:** 
1. Asegúrate de que el servidor esté corriendo
2. Recarga la página (F5)
3. Intenta generar el QR de nuevo

### Error: No aparece el código
**Solución:**
1. Verifica que el servidor esté activo
2. Selecciona el cliente nuevamente
3. Click en "Generar QR"

---

## 🔄 FLUJO COMPLETO

```
1. Iniciar INICIAR-SERVIDOR.bat (dejar abierto)
2. Abrir navegador → http://localhost:8000
3. Login al sistema
4. Ir a "Códigos QR"
5. Seleccionar cliente
6. Click "Generar QR" → Copiar código
7. Click "Leer Código QR" → Pegar código
8. Click "Buscar Envío"
9. Click "Ver Documento del Envío"
10. ¡Listo! PDF completo del envío
```

---

## 📞 TIPS

- ✅ Mantén el terminal abierto mientras uses la aplicación
- ✅ Copia y pega los códigos para evitar errores
- ✅ Los códigos QR se pueden descargar como imágenes PNG
- ✅ Cada envío tiene su código único (ej: ENV-XXXXXXXXXX)
- ✅ El PDF se genera en tiempo real con toda la información actualizada

---

**¡Importante para tu presentación con el docente!**

Antes de presentar:
1. Inicia el servidor (`INICIAR-SERVIDOR.bat`)
2. Verifica que puedas generar QR
3. Prueba escanear un código
4. Asegúrate de que el PDF se genere correctamente
5. Mantén el terminal abierto durante toda la presentación

**¡Buena suerte! 🎓**
