<?php

namespace App\Services;

use App\Models\Herramienta;
use App\Models\Usuario;
use App\Models\Vale;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CortexSecurityService
{
    /**
     * Analiza el sistema en busca de brechas o anomalías de seguridad.
     */
    public function performSecurityScan()
    {
        $incidents = [];
        $dismissed = session()->get('cortex_dismissed_incidents', []);

        // 1. Detección de borrados masivos
        $massDeletions = Herramienta::onlyTrashed()
            ->where('deleted_at', '>=', Carbon::now()->subHours(12))
            ->count();
        
        if ($massDeletions > 10 && !in_array('mass_deletions', $dismissed)) {
            $incidents[] = [
                'key' => 'mass_deletions',
                'type' => 'SECURITY',
                'severity' => 'CRITICAL',
                'message' => "ALERTA DE SABOTAJE: Se han eliminado {$massDeletions} activos en un periodo de 12 horas.",
                'meta' => ['count' => $massDeletions]
            ];
            CortexAuditorService::log('SECURITY', "Detección de borrado masivo ({$massDeletions} registros)", 'CRITICAL');
        }

        // 2. Creación sospechosa de Administradores
        $newAdmins = Usuario::where('rol', 'Administrador')
            ->where('creado_en', '>=', Carbon::now()->subHours(24))
            ->count();

        if ($newAdmins > 1 && !in_array('new_admins', $dismissed)) {
            $incidents[] = [
                'key' => 'new_admins',
                'type' => 'SECURITY',
                'severity' => 'HIGH',
                'message' => "ANOMALÍA DE ACCESO: Se detectaron {$newAdmins} nuevos perfiles administrativos en 24h.",
                'meta' => ['count' => $newAdmins]
            ];
            CortexAuditorService::log('SECURITY', "Múltiples administradores creados recientemente", 'HIGH');
        }

        // 3. Verificación de integridad de Vales (Vales huérfanos o sin firmas)
        $orphanVales = Vale::whereDoesntHave('trabajador')->count();
        if ($orphanVales > 0 && !in_array('orphan_vales', $dismissed)) {
            $incidents[] = [
                'key' => 'orphan_vales',
                'type' => 'SYSTEM',
                'severity' => 'MEDIUM',
                'message' => "CORRUPCIÓN DE DATOS: Se detectaron {$orphanVales} vales huérfanos sin relación de trabajador.",
                'meta' => ['count' => $orphanVales]
            ];
        }

        return $incidents;
    }

    /**
     * Calcula el nivel de riesgo actual del sistema.
     */
    public function getRiskLevel()
    {
        $incidents = $this->performSecurityScan();
        $critical = collect($incidents)->where('severity', 'CRITICAL')->count();
        $high = collect($incidents)->where('severity', 'HIGH')->count();

        if ($critical > 0) return ['level' => 'CRITICAL', 'score' => 95, 'color' => '#F43F5E'];
        if ($high > 0) return ['level' => 'HIGH', 'score' => 70, 'color' => '#F59E0B'];
        return ['level' => 'LOW', 'score' => 12, 'color' => '#64FFDA'];
    }
}
