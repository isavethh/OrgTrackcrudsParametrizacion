#!/bin/sh
set -e

echo "Esperando a Postgres..."

# Esperamos a que la DB responda
until pg_isready -h "$DB_HOST" -p "$DB_PORT" -U "$DB_USERNAME" 2>/dev/null; do
  echo "Postgres no está listo, reintentando..."
  sleep 2
done

echo "✓ Base de datos lista!"

# Generar key si no existe
if [ ! -f /var/www/storage/app/installed.flag ]; then
    echo "Generando application key..."
    php artisan key:generate --force
    touch /var/www/storage/app/installed.flag
    echo "✓ Key generada"
fi

# Migraciones
echo "Ejecutando migraciones..."
php artisan migrate --force
echo "✓ Migraciones completadas"

echo "Ejecutando Seeder..."
php artisan db:seed --force
echo "✓ Seeder completadas"

# Caché de configuración para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "🚀 Iniciando servicios..."

# Iniciar PHP-FPM en background
php-fpm -D

# Iniciar Nginx en foreground
exec nginx -g 'daemon off;'