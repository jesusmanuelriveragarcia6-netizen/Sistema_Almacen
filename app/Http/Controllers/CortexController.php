<?php

namespace App\Http\Controllers;

use App\Services\AIService;
use App\Services\SystemValidatorService;
use Illuminate\Http\Request;

class CortexController extends Controller
{
    public function index(AIService $aiService)
    {
        try {
            $aiInsights = $aiService->getInsights();
        } catch (\Exception $e) {
            \Log::error("Error en Cortex Service: " . $e->getMessage());
            $aiInsights = [
                'anomalies' => [],
                'recommendations' => [],
                'load_analysis' => collect([]),
                'trends' => ['activity_score' => 0, 'momentum' => 0, 'prediction' => 'Indeterminado'],
                'summary' => 'El núcleo de inteligencia está experimentando latencia en la sincronización de logs.',
                'security' => ['status' => 'OFFLINE', 'alerts' => [], 'level' => 'LOW'],
                'diagnostic' => [],
                'neural_logs' => [],
                'security_incidents' => [],
                'risk_assessment' => ['level' => 'UNKNOWN', 'score' => 0, 'color' => '#8892B0']
            ];
        }

        return view('cortex.index', compact('aiInsights'));
    }

    public function runFullScan()
    {
        $validator = new SystemValidatorService();
        $results = $validator->runFullDiagnostic();
        
        $hasErrors = false;
        foreach($results as $res) {
            if($res['status'] === 'FAIL') $hasErrors = true;
        }

        return redirect()->route('cortex.index')->with([
            'scan_completed' => true,
            'scan_errors' => $hasErrors
        ]);
    }

    public function repair(Request $request, $component)
    {
        // Solo administradores pueden autorizar reparaciones críticas
        if (auth()->user()->rol !== 'Administrador') {
            return response()->json(['success' => false, 'message' => 'Autorización denegada.'], 403);
        }

        $automation = new \App\Services\CortexAutomationService();
        try {
            $message = $automation->executeAuthorizedRepair($component);
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function dismissIncident(Request $request)
    {
        $key = $request->input('key');
        
        if (!$key) {
            return response()->json(['success' => false, 'message' => 'Identificador de incidente no proporcionado.'], 400);
        }

        $dismissed = session()->get('cortex_dismissed_incidents', []);
        
        if (!in_array($key, $dismissed)) {
            $dismissed[] = $key;
            session()->put('cortex_dismissed_incidents', $dismissed);
        }

        // Registrar evento de seguridad auditado
        \App\Services\CortexAuditorService::log(
            'SECURITY', 
            "Incidente '{$key}' mitigado y marcado como resuelto por el administrador", 
            'LOW'
        );

        return response()->json([
            'success' => true, 
            'message' => 'El incidente ha sido marcado como resuelto/mitigado de manera exitosa y el riesgo ha sido actualizado.'
        ]);
    }

    public function restoreIncidents()
    {
        session()->forget('cortex_dismissed_incidents');
        return response()->json([
            'success' => true,
            'message' => 'Todas las alertas e incidentes archivados han sido restaurados exitosamente.'
        ]);
    }
}
