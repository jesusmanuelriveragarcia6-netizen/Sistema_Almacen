<?php

namespace App\Services;

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

class CortexAutomationService
{
    /**
     * Ejecuta una reparación autorizada.
     */
    public function executeAuthorizedRepair($protocol)
    {
        $message = "";
        $severity = "INFO";

        switch ($protocol) {
            case 'purge_cache':
                Artisan::call('cache:clear');
                Artisan::call('view:clear');
                Artisan::call('config:clear');
                $message = "Protocolo de purga completado: Memoria volátil liberada.";
                break;

            case 'optimize_db':
                // Optimización real de índices y tablas en MySQL/MariaDB
                try {
                    $tables = ['herramientas', 'usuarios', 'vales', 'trabajadores', 'almacenes', 'cortex_events', 'incidencias'];
                    $optimizedTables = [];
                    foreach ($tables as $table) {
                        if (\Illuminate\Support\Facades\Schema::hasTable($table)) {
                            \Illuminate\Support\Facades\DB::statement("OPTIMIZE TABLE {$table}");
                            $optimizedTables[] = $table;
                        }
                    }
                    $message = "Optimización de base de datos finalizada: Tablas desfragmentadas e índices recalculados (" . implode(', ', $optimizedTables) . ").";
                    $severity = "SYSTEM";
                } catch (\Exception $e) {
                    \Log::error("Fallo al optimizar base de datos: " . $e->getMessage());
                    throw new \Exception("Fallo al optimizar base de datos: " . $e->getMessage());
                }
                break;

            case 'fix_permissions':
                // Corrección real de permisos en storage y bootstrap/cache de manera segura e independiente del SO (Windows/Linux)
                try {
                    $paths = [
                        storage_path(),
                        storage_path('app'),
                        storage_path('framework'),
                        storage_path('framework/cache'),
                        storage_path('framework/sessions'),
                        storage_path('framework/views'),
                        storage_path('logs'),
                        base_path('bootstrap/cache')
                    ];

                    $fixedCount = 0;
                    foreach ($paths as $path) {
                        if (file_exists($path)) {
                            // Cambiar atributos usando PHP chmod
                            @chmod($path, 0775);
                            $fixedCount++;
                            
                            // Asegurar subdirectorios y archivos de manera recursiva
                            if (is_dir($path)) {
                                $iterator = new \RecursiveIteratorIterator(
                                    new \RecursiveDirectoryIterator($path, \RecursiveDirectoryIterator::SKIP_DOTS),
                                    \RecursiveIteratorIterator::SELF_FIRST
                                );
                                
                                foreach ($iterator as $item) {
                                    @chmod($item->getPathname(), $item->isDir() ? 0775 : 0664);
                                    $fixedCount++;
                                }
                            }
                        }
                    }

                    $message = "Permisos de sistema restaurados en directorios críticos ({$fixedCount} elementos procesados).";
                    $severity = "SYSTEM";
                } catch (\Exception $e) {
                    \Log::error("Fallo al corregir permisos: " . $e->getMessage());
                    throw new \Exception("Fallo al corregir permisos: " . $e->getMessage());
                }
                break;

            default:
                throw new \Exception("Protocolo no reconocido.");
        }

        CortexAuditorService::log('SYSTEM_FIX', $message, 'MEDIUM', ['protocol' => $protocol], true);

        return $message;
    }
}
