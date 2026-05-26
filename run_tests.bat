@echo off
color 0A
echo ========================================================
echo   SISTEMA DE EVALUACION Y TESTING AUTOMATIZADO (LARAVEL)
echo ========================================================
echo.

echo [1/4] Limpiando cache del sistema para evitar falsos positivos...
call php artisan optimize:clear
echo.

echo [2/4] Validando sintaxis y analizando el código...
:: Si tienes PHPStan o Pint instalado, aquí se pueden ejecutar
:: call ./vendor/bin/pint --test
echo (Analisis de sintaxis omitido por ahora, puedes agregar Laravel Pint aqui)
echo.

echo [3/4] Ejecutando pruebas y guardando resultados...
call php artisan test > storage\logs\test_results.txt 2>&1
type storage\logs\test_results.txt

echo.
echo [4/4] Analizando resultados con Asistente de Voz...
call python scripts\voz.py storage\logs\test_results.txt

echo.
echo ========================================================
echo   EVALUACION COMPLETADA
echo ========================================================
pause
