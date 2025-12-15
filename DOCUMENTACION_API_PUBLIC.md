# 📡 Documentación de API Pública - Envíos (OrgTrack)

Esta API permite la integración externa para la creación y seguimiento de envíos sin necesidad de autenticación (JWT).

**Nota Importante:** Todas las rutas incluyen el prefijo `/api` porque están definidas en el archivo de rutas de API de Laravel.

---

## 1. Crear Dirección
**Descripción:** Crea un registro de dirección (origen y destino) necesario antes de crear un envío.
- **Método:** `POST`
- **URL Completa:** `/api/public/direccion`
- **Body JSON:**
  ```json
  {
      "nombreorigen": "Calle 123, Zona Sur",
      "nombredestino": "Av. Principal 456, Centro",
      "origen_lat": -16.5000,
      "origen_lng": -68.1500,
      "destino_lat": -17.3895,
      "destino_lng": -66.1568,
      "rutageojson": "{...}" // Opcional: GeoJSON de la ruta
  }
  ```
- **Respuesta:**
  ```json
  {
      "mensaje": "Dirección creada exitosamente",
      "id_direccion": 45
  }
  ```

---

## 2. Crear Envío de Productor
**Descripción:** Crea el envío vinculándolo a la dirección creada previamente.
- **Método:** `POST`
- **URL Completa:** `/api/public/envios`
- **Body JSON:**
  ```json
  {
      "nombre_remitente": "Juan Perez",
      "telefono_remitente": "70012345",
      "email_remitente": "juan@email.com",
      "id_direccion": 45, // ID obtenido del endpoint anterior
      "numero_solicitud": "SOL-001", // Opcional
      "particiones": [
          {
              "id_tipo_transporte": 1,
              "recogidaEntrega": {
                  "fecha_recogida": "2024-12-15",
                  "hora_recogida": "08:00",
                  "hora_entrega": "18:00",
                  "instrucciones_recogida": "Puerta trasera"
              },
              "cargas": [
                  {
                      "tipo": "Fruta", 
                      "variedad": "Manzana",
                      "empaquetado": "Cajas",
                      "cantidad": 100,
                      "peso": 500,
                      "largo_cm": 50, // Opcional
                      "peso_neto_kg": 500 // Opcional
                  }
              ]
          }
      ]
  }
  ```

---

## 3. Crear Envío desde Solicitud de Materiales (Integración)
**Descripción:** Transforma una solicitud de materiales externa en un envío directo.
- **Método:** `POST`
- **URL Completa:** `/api/public/envios/from-material-request`
- **Body JSON:**
  ```json
  {
      "request_number": "REQ-100",
      "required_date": "2024-12-20",
      "nombre_remitente": "Proveedor Industrial",
      "telefono_remitente": "77777777",
      "id_direccion_origen": 10,
      "id_direccion_destino": 20,
      "id_tipo_transporte": 1,
      "fecha_recogida": "2024-12-19",
      "hora_recogida": "09:00",
      "hora_entrega": "11:00",
      "materials": [
          {
              "material_name": "Cemento",
              "requested_quantity": 50,
              "unit": "Bolsas"
          }
      ]
  }
  ```

---

## 4. Obtener Seguimiento (Tracking)
**Descripción:** Obtiene los detalles públicos de un envío para rastreo.
- **Método:** `GET`
- **URL Completa:** `/api/public/envios/{id}/seguimiento`

---

## 5. Listar Envíos Entregados
**Descripción:** Lista los envíos públicos que ya han sido finalizados.
- **Método:** `GET`
- **URL Completa:** `/api/public/envios`

---

## 6. Listar Todos los Envíos
**Descripción:** Lista todos los envíos públicos independientemente de su estado.
- **Método:** `GET`
- **URL Completa:** `/api/public/envios/all`

---

## 7. Generar Documento PDF
**Descripción:** Obtiene los datos para generar el comprobante de entrega (solo si está Entregado).
- **Método:** `GET`
- **URL Completa:** `/api/public/envios/{id_envio}/documento`
