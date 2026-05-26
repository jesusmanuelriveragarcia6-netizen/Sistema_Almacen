<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CortexAuditorService
{
    /**
     * Registra un evento en el núcleo de Cortex.
     */
    public static function log($type, $message, $severity = 'INFO', $metadata = [], $authorized = false)
    {
        try {
            DB::table('cortex_events')->insert([
                'type' => $type,
                'severity' => $severity,
                'message' => $message,
                'metadata' => json_encode($metadata),
                'user_id' => Auth::id(),
                'is_authorized' => $authorized,
                'created_at' => now()
            ]);
        } catch (\Exception $e) {
            \Log::error("Fallo al registrar evento Cortex: " . $e->getMessage());
        }
    }

    /**
     * Obtiene los últimos eventos del sistema.
     */
    public function getRecentEvents($limit = 15)
    {
        return DB::table('cortex_events')
            ->leftJoin('usuarios', 'cortex_events.user_id', '=', 'usuarios.id')
            ->select('cortex_events.*', 'usuarios.nombre as user_name')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }
}
