#  Guía de Despliegue del Sistema - OrgTrack

Este documento detalla los pasos necesarios para instalar y ejecutar el sistema OrgTrack, tanto utilizando **Docker** (método recomendado) como de forma **Manual**.

---

##  Requisitos Previos

Antes de comenzar, asegúrate de tener instalado lo siguiente según el método que elijas:

### Para Despliegue con Docker 
- **Docker Desktop** (o Docker Engine + Docker Compose) instalado y corriendo.
- Puerto **80** libre en tu máquina (o modificar `docker-compose.yml` si está ocupado).

### Para Despliegue Manual 
- **PHP**: Versión 8.2 o superior.
- **Composer**: Gestor de dependencias de PHP.
- **Node.js & NPM**: Para compilar los activos del frontend (Vite/Tailwind).
- **Base de Datos**: PostgreSQL (Recomendado) o MySQL/MariaDB/SQLite.

---

##  Opción 1: Despliegue con Docker (Recomendado)

Este proyecto ya incluye una configuración de Docker robusta que se encarga de instalar dependencias, configurar la base de datos y preparar el entorno automáticamente.

### Pasos:

1.  **Abrir una terminal** en la raíz del proyecto.

2.  **Construir y levantar los contenedores**:
    Ejecuta el siguiente comando para construir las imágenes y levantar los servicios en segundo plano:
    ```bash
    docker-compose up -d --build
    ```

3.  **Esperar la inicialización**:
    El contenedor `laravel` ejecutará un script de entrada (`entrypoint.sh`) que automáticamente:
    - Creará el archivo `.env` si no existe.
    - Instalará las dependencias de PHP (`composer install`).
    - Generará la clave de aplicación.
    - Ejecutará las migraciones y seeders de la base de datos.
    - Iniciará el servidor.

    puedes monitorear el progreso con:
    ```bash
    docker-compose logs -f laravel
    ```
    *Espera hasta ver " Iniciando PHP-FPM..."*

4.  **Acceder al sistema**:
    Abre tu navegador y visita:
    - **URL**: `http://localhost`

---

## Opción 2: Despliegue Manual (Sin Docker)

Si prefieres ejecutar el sistema directamente en tu servidor o máquina local.

### 1. Configuración del Entorno

1.  **Copiar archivo de configuración**:
    ```bash
    cp .env.example .env
    ```

2.  **Editar `.env`**:
    Abre el archivo `.env` y configura tu conexión a la base de datos.
    
    *Ejemplo para PostgreSQL:*
    ```ini
    DB_CONNECTION=pgsql
    DB_HOST=127.0.0.1
    DB_PORT=5432
    DB_DATABASE=nombre_de_tu_bd
    DB_USERNAME=tu_usuario
    DB_PASSWORD=tu_password
    ```
    *Nota: Asegúrate de crear la base de datos vacía en tu gestor de BD antes de continuar.*

    **Importante**: Configura también las variables del Helpdesk si las tienes:
    ```ini
    HELPDESK_API_URL=https://proyecto-de-ultimo-minuto.online
    HELPDESK_API_KEY=tu_api_key_aqui
    ```

### 2. Instalación de Dependencias

1.  **Backend (PHP)**:
    ```bash
    composer install
    ```

2.  **Frontend (Node.js)**:
    ```bash
    npm install
    npm run build
    ```

### 3. Inicialización del Sistema

1.  **Generar clave de aplicación**:
    ```bash
    php artisan key:generate
    ```

2.  **Ejecutar migraciones y datos de prueba (Seeders)**:
    Esto creará las tablas y usuarios por defecto.
    ```bash
    php artisan migrate --seed
    ```

3.  **Enlace simbólico para almacenamiento** (Opcional pero recomendado para imágenes):
    ```bash
    php artisan storage:link
    ```

### 4. Ejecutar el Servidor

Para desarrollo local, puedes usar el servidor integrado de Laravel:

```bash
php artisan serve
```

El sistema estará disponible en: `http://localhost:8000`

---

## Solución de Problemas Comunes

### Permisos de Carpetas (Linux/Mac)
Si tienes errores de permisos al escribir logs o sesiones en modo manual:
```bash
chmod -R 775 storage bootstrap/cache
```

### Error de Conexión a Base de Datos (Docker)
Asegúrate de que no haya otro servicio (como un Postgres local) ocupando el puerto `5432`. Si es así, detenlo o cambia el mapeo de puertos en `docker-compose.yml`.

### Los estilos no cargan
Asegúrate de haber ejecutado `npm run build`. Si estás en desarrollo, puedes dejar corriendo `npm run dev` en otra terminal para carga dinámica.
