@echo off
echo ========================================
echo    SERVIDOR LARAVEL - OrgTrack
echo ========================================
echo.
echo Iniciando servidor en http://localhost:8000
echo.
echo IMPORTANTE: NO CIERRES ESTA VENTANA
echo El servidor debe estar corriendo para usar la aplicacion
echo.
echo Para detener el servidor presiona Ctrl+C
echo ========================================
echo.

cd /d "%~dp0"
php artisan serve

pause
