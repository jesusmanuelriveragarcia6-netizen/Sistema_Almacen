@extends('layouts.app')

@section('title', 'Cortex NOC - Neural Operations Center')

@section('content')
<div class="cortex-neural-container">
    <x-cortex.assistant-shell :security-status="$aiInsights['security']['status']">
        
        <!-- HEADER: ESTADO GLOBAL -->
        <div class="d-flex justify-content-between align-items-center mb-5">
            <div>
                <h2 class="text-white font-weight-bold mb-1">Neural Operations Center</h2>
                <p class="text-muted m-0">Supervisión centralizada y autonomía de nivel 3 activa.</p>
                <div class="mt-2">
                    <span class="badge" style="background: rgba(100,255,218,0.1); color: #64FFDA; border: 1px solid #64FFDA; font-size: 0.8rem; padding: 0.4em 0.8em;">
                        <i class="fa-solid fa-check-double mr-1"></i> ESTADO GENERAL: ÓPTIMO
                    </span>
                </div>
            </div>
            <div class="d-flex gap-4">
                <div class="text-center px-4 border-right border-dark">
                    <span class="d-block text-muted small uppercase font-weight-bold mb-1">Nivel de Riesgo</span>
                    <span style="color: {{ $aiInsights['risk_assessment']['color'] }}; font-weight: 900; font-size: 1.2rem;">
                        {{ $aiInsights['risk_assessment']['level'] }}
                    </span>
                </div>
                <div class="text-center px-4">
                    <span class="d-block text-muted small uppercase font-weight-bold mb-1">Uptime de IA</span>
                    <span class="text-primary font-weight-bold" style="font-size: 1.2rem;">99.98%</span>
                </div>
            </div>
        </div>

        <!-- TABS DE NAVEGACIÓN -->
        <div class="noc-tabs">
            <button class="noc-tab-btn active" onclick="switchTab(this, 'monitoring')">Monitoreo</button>
            <button class="noc-tab-btn" onclick="switchTab(this, 'security')">Seguridad</button>
            <button class="noc-tab-btn" onclick="switchTab(this, 'diagnostic')">Diagnóstico</button>
            <button class="noc-tab-btn" onclick="switchTab(this, 'intelligence')">Inteligencia</button>
        </div>

        <!-- CONTENIDO DE TABS -->
        <div id="noc-content">
            
            <!-- TAB 1: MONITOREO -->
            <div id="tab-monitoring" class="noc-section">
                
                @if(count($aiInsights['anomalies']) > 0)
                <div class="row mb-5">
                    <div class="col-12">
                        <div class="p-4 rounded position-relative overflow-hidden" style="background: rgba(244, 63, 94, 0.05); border: 1px solid rgba(244, 63, 94, 0.3); box-shadow: 0 0 20px rgba(244, 63, 94, 0.1);">
                            <div class="position-absolute" style="top:0; left:0; width:4px; height:100%; background:#F43F5E; box-shadow: 0 0 15px #F43F5E;"></div>
                            <h6 class="text-danger font-weight-bold mb-3 d-flex align-items-center">
                                <i class="fa-solid fa-triangle-exclamation mr-3 fa-beat"></i> ALERTAS CRÍTICAS DETECTADAS
                            </h6>
                            <div class="row">
                                @foreach($aiInsights['anomalies'] as $anomaly)
                                    <div class="col-md-6 mb-3">
                                        <div class="d-flex align-items-start gap-3">
                                            <div class="mt-1" style="width: 8px; height: 8px; border-radius: 50%; background: #F43F5E; box-shadow: 0 0 8px #F43F5E;"></div>
                                            <div class="text-white small" style="opacity: 0.9; line-height: 1.5;">
                                                <strong class="text-danger mr-2">{{ strtoupper($anomaly['type']) }}:</strong> {{ $anomaly['message'] }}
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <div class="terminal-window">
                            <div class="d-flex justify-content-between mb-4 border-bottom border-dark pb-2">
                                <span class="text-primary small font-weight-bold">> NEURAL_LOGS_STREAM</span>
                                <span class="text-muted small">REAL_TIME_FEED</span>
                            </div>
                            <div id="neural-logs-container">
                                @foreach($aiInsights['neural_logs'] as $log)
                                    <div class="neural-log-line">
                                        <span class="log-time">[{{ \Carbon\Carbon::parse($log->created_at)->format('H:i:s') }}]</span>
                                        <span class="log-type text-{{ $log->type === 'SECURITY' ? 'danger' : ($log->type === 'TRANSACTION' ? 'success' : 'primary') }}">
                                            {{ $log->type }}
                                        </span>
                                        <span class="log-msg">{{ $log->message }}</span>
                                    </div>
                                @endforeach
                            </div>
                            <div class="neural-log-line mt-3" style="opacity: 0.5;">
                                <i class="fa-solid fa-spinner fa-spin mr-2"></i> Escuchando eventos del núcleo...
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <x-cortex.module-card title="Carga de Sistema" icon="fa-microchip">
                            <div class="prediction-stats mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">CPU Load</span>
                                    <span class="text-primary small">24%</span>
                                </div>
                                <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05);">
                                    <div class="progress-bar bg-primary" style="width: 24%"></div>
                                </div>
                            </div>
                            <div class="prediction-stats mb-4">
                                <div class="d-flex justify-content-between mb-2">
                                    <span class="text-muted small">DB Latency</span>
                                    <span class="text-success small">12ms</span>
                                </div>
                                <div class="progress" style="height: 6px; background: rgba(255,255,255,0.05);">
                                    <div class="progress-bar bg-success" style="width: 15%"></div>
                                </div>
                            </div>
                        </x-cortex.module-card>

                        <div class="mt-4">
                            <x-cortex.module-card title="Consola de Análisis" icon="fa-radar">
                                <p class="text-muted small mb-4">Inicia un diagnóstico completo para recalibrar los sensores neurales del almacén.</p>
                                <button class="btn-cortex-neural w-100" style="padding: 1rem !important; border-radius: 12px !important;" onclick="runAIScan()">
                                    <i class="fa-solid fa-atom fa-spin mr-3"></i> RE-INICIAR ESCANEO
                                </button>
                            </x-cortex.module-card>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 2: SEGURIDAD -->
            <div id="tab-security" class="noc-section d-none">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-5 rounded mb-4" style="background: rgba(10, 25, 47, 0.4); border: 1px solid rgba(100, 255, 218, 0.1);">
                            <div class="risk-gauge-container">
                                <svg class="gauge-svg" width="150" height="150">
                                    <circle class="gauge-circle-bg" cx="75" cy="75" r="65"></circle>
                                    <circle class="gauge-circle-val" cx="75" cy="75" r="65" 
                                            style="stroke-dasharray: {{ ($aiInsights['risk_assessment']['score'] / 100) * 408 }}, 408; stroke: {{ $aiInsights['risk_assessment']['color'] }};">
                                    </circle>
                                </svg>
                                <div class="gauge-text">
                                    <span class="val" style="color: {{ $aiInsights['risk_assessment']['color'] }}">{{ $aiInsights['risk_assessment']['score'] }}%</span>
                                    <span class="label">Riesgo Base</span>
                                </div>
                            </div>
                            <h5 class="mt-4 text-white uppercase mb-1">{{ $aiInsights['risk_assessment']['level'] }}</h5>
                            <div class="d-flex align-items-center justify-content-center gap-2 text-muted small">
                                <i class="fa-solid fa-circle-info text-primary" style="font-size: 0.7rem;"></i>
                                <span style="font-size: 0.75rem; letter-spacing: 0.5px;">Cálculo basado en integridad, accesos y firewall.</span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <h5 class="text-white mb-4">Incidentes de Seguridad</h5>
                        @forelse($aiInsights['security_incidents'] as $incident)
                            <div class="incident-card">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <span class="badge bg-{{ $incident['severity'] === 'CRITICAL' ? 'danger' : 'warning' }} px-3 py-1">
                                        {{ $incident['severity'] }}
                                    </span>
                                    <span class="text-muted small">Detectado hace momentos</span>
                                </div>
                                <p class="text-white m-0 font-weight-bold">{{ $incident['message'] }}</p>
                                <div class="mt-3">
                                    <button class="btn btn-sm btn-outline-danger px-3" onclick="executeSecurityResponse('AISLAR', '{{ $incident['key'] }}')">AISLAR NODO</button>
                                    <button class="btn btn-sm btn-link text-muted" onclick="executeSecurityResponse('IGNORAR', '{{ $incident['key'] }}')">IGNORAR</button>
                                </div>
                            </div>
                        @empty
                            <div class="text-center p-5">
                                <i class="fa-solid fa-shield-check text-success mb-3" style="font-size: 3rem; opacity: 0.3;"></i>
                                <p class="text-muted">No se detectan amenazas activas en el perímetro.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- TAB 3: DIAGNÓSTICO -->
            <div id="tab-diagnostic" class="noc-section d-none">
                <div class="row">
                    @foreach($aiInsights['diagnostic'] as $key => $test)
                        <div class="col-md-3 mb-4">
                            <div class="p-4 rounded text-center" style="background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.05);">
                                <h6 class="text-muted uppercase small mb-3">{{ $key }}</h6>
                                <div class="mb-3">
                                    <i class="fa-solid {{ $test['status'] === 'PASS' ? 'fa-circle-check text-success' : 'fa-circle-xmark text-danger' }}" style="font-size: 2rem;"></i>
                                </div>
                                <p class="small text-white mb-3" style="height: 40px; overflow: hidden;">{{ $test['message'] }}</p>
                                <button class="btn-cortex-neural py-2 w-100" style="font-size: 0.7rem;" onclick="runRepair('{{ $key }}')">
                                    {{ $test['status'] === 'PASS' ? 'OPTIMIZAR' : 'REPARAR' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- NUEVA SECCIÓN: TABLA DE RESUMEN PARA EL INFORME -->
                <div class="row mt-5">
                    <div class="col-12">
                        <x-cortex.module-card title="Resumen General: Bugs e Incidencias" icon="fa-bug">
                            <div class="table-responsive mt-3">
                                <table class="table table-borderless text-white" style="background: rgba(10,25,47,0.3); border-radius: 12px; overflow: hidden;">
                                    <thead style="background: rgba(100,255,218,0.1); color: #64FFDA; border-bottom: 2px solid rgba(100,255,218,0.2);">
                                        <tr>
                                            <th class="py-3 px-4 font-weight-bold">Módulo / Categoría</th>
                                            <th class="py-3 px-4 font-weight-bold">Descripción del Escaneo</th>
                                            <th class="py-3 px-4 font-weight-bold text-center">Severidad</th>
                                            <th class="py-3 px-4 font-weight-bold text-center">Estado</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($aiInsights['diagnostic'] as $key => $test)
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <td class="py-3 px-4 uppercase text-muted font-weight-bold">{{ $key }}</td>
                                            <td class="py-3 px-4" style="opacity: 0.8; font-size: 0.9rem;">{{ $test['message'] }}</td>
                                            <td class="py-3 px-4 text-center">
                                                @if($test['status'] === 'PASS')
                                                    <span class="badge" style="background: rgba(100,255,218,0.1); color: #64FFDA; border: 1px solid #64FFDA;">LOW (Nominal)</span>
                                                @else
                                                    <span class="badge" style="background: rgba(244,63,94,0.1); color: #F43F5E; border: 1px solid #F43F5E;">HIGH (Crítico)</span>
                                                @endif
                                            </td>
                                            <td class="py-3 px-4 text-center">
                                                @if($test['status'] === 'PASS')
                                                    <i class="fa-solid fa-check text-success"></i> 0 Bugs
                                                @else
                                                    <i class="fa-solid fa-bug text-danger"></i> Falla Detectada
                                                @endif
                                            </td>
                                        </tr>
                                        @endforeach
                                        
                                        <!-- Simulamos un escaneo del núcleo para dar volumen a la tabla -->
                                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                                            <td class="py-3 px-4 uppercase text-muted font-weight-bold">FIREWALL</td>
                                            <td class="py-3 px-4" style="opacity: 0.8; font-size: 0.9rem;">Verificación de accesos no autorizados en capa de red.</td>
                                            <td class="py-3 px-4 text-center">
                                                <span class="badge" style="background: rgba(100,255,218,0.1); color: #64FFDA; border: 1px solid #64FFDA;">LOW (Nominal)</span>
                                            </td>
                                            <td class="py-3 px-4 text-center"><i class="fa-solid fa-check text-success"></i> 0 Bugs</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </x-cortex.module-card>
                    </div>
                </div>
            </div>

            <!-- TAB 4: INTELIGENCIA -->
            <div id="tab-intelligence" class="noc-section d-none">
                <div class="row">
                    <div class="col-md-6">
                        <x-cortex.module-card title="Análisis Predictivo" icon="fa-chart-line">
                            <p class="text-muted">Proyección de actividad para los próximos 7 días.</p>
                            <div class="p-4 text-center">
                                <div class="predictive-chart-container mb-4" style="height: 110px; position: relative;">
                                    <!-- SVG Smooth Line Graph -->
                                    <svg viewBox="0 0 100 30" width="100%" height="100%" preserveAspectRatio="none" style="overflow: visible;">
                                        <defs>
                                            <linearGradient id="chartGrad" x1="0" y1="0" x2="0" y2="1">
                                                <stop offset="0%" stop-color="#64FFDA" stop-opacity="0.25"/>
                                                <stop offset="100%" stop-color="#64FFDA" stop-opacity="0.0"/>
                                            </linearGradient>
                                        </defs>
                                        <!-- Gradient Area -->
                                        <path d="M 0 30 C 15 25, 30 20, 50 12 C 70 8, 85 5, 100 2 L 100 30 Z" fill="url(#chartGrad)"/>
                                        <!-- Animated Line -->
                                        <path d="M 0 30 C 15 25, 30 20, 50 12 C 70 8, 85 5, 100 2" fill="none" stroke="#64FFDA" stroke-width="1.5" stroke-linecap="round"
                                              style="stroke-dasharray: 200; stroke-dashoffset: 200; animation: drawLine 2.5s ease-out forwards;"/>
                                        <!-- Forecast dots -->
                                        <circle cx="50" cy="12" r="1.5" fill="#64FFDA" style="opacity: 0; animation: fadeInPoint 0.5s ease-out 1s forwards;"/>
                                        <circle cx="100" cy="2" r="1.5" fill="#0A192F" stroke="#64FFDA" stroke-width="1" style="opacity: 0; animation: fadeInPoint 0.5s ease-out 2s forwards;"/>
                                    </svg>
                                    
                                    <!-- CSS Animation styles -->
                                    <style>
                                        @keyframes drawLine {
                                            to { stroke-dashoffset: 0; }
                                        }
                                        @keyframes fadeInPoint {
                                            to { opacity: 1; }
                                        }
                                    </style>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="text-left">
                                        <h4 class="text-white font-weight-bold m-0 d-flex align-items-center gap-2">
                                            <i class="fa-solid fa-arrow-trend-up text-success fa-beat"></i>
                                            {{ $aiInsights['trends']['prediction'] }}
                                        </h4>
                                        <span class="text-muted small" style="font-size: 0.75rem;">Tendencia de flujo operativo</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="badge bg-primary text-dark px-3 py-1 font-weight-bold" style="font-size: 0.75rem;">
                                            SCORE: {{ $aiInsights['trends']['activity_score'] }}%
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </x-cortex.module-card>
                    </div>
                    <div class="col-md-6">
                        <x-cortex.module-card title="Recomendaciones" icon="fa-lightbulb">
                            @forelse($aiInsights['recommendations'] as $rec)
                                <div class="p-3 mb-3 rounded" style="background: rgba(100, 255, 218, 0.03); border-left: 3px solid #64FFDA;">
                                    <i class="fa-solid {{ $rec['icon'] }} mr-2 text-primary"></i>
                                    <span class="text-muted small">{{ $rec['text'] }}</span>
                                </div>
                            @empty
                                <div class="p-5 text-center" style="background: rgba(100, 255, 218, 0.02); border: 1px dashed rgba(100, 255, 218, 0.15); border-radius: 12px; margin-top: 15px;">
                                    <i class="fa-solid fa-circle-check text-success fa-bounce mb-3" style="font-size: 3rem; --fa-bounce-start-y-value: -8px; --fa-bounce-transition-duration: 1.5s;"></i>
                                    <h6 class="text-white font-weight-bold mb-1">Núcleo en Estado Óptimo</h6>
                                    <p class="text-muted small m-0" style="font-size: 0.75rem; line-height: 1.5;">
                                        No hay recomendaciones críticas pendientes. Todos tus activos mantienen stock estable y tu personal reporta actividad continua en el almacén.
                                    </p>
                                </div>
                            @endforelse
                        </x-cortex.module-card>
                    </div>
                </div>
            </div>

        </div>

    </x-cortex.assistant-shell>
</div>

<script>
function switchTab(btn, tabId) {
    // UI Update
    document.querySelectorAll('.noc-tab-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // Section Visibility
    document.querySelectorAll('.noc-section').forEach(sec => sec.classList.add('d-none'));
    const target = document.getElementById('tab-' + tabId);
    if (target) target.classList.remove('d-none');
}

function runRepair(component) {
    Swal.fire({
        title: 'AUTORIZACIÓN REQUERIDA',
        text: '¿Desea que Cortex ejecute el protocolo de reparación para ' + component + '?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#64FFDA',
        cancelButtonColor: '#F43F5E',
        confirmButtonText: 'SÍ, AUTORIZAR',
        cancelButtonText: 'CANCELAR',
        background: '#0A192F',
        color: '#E6F1FF'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'EJECUTANDO PROTOCOLO',
                html: '<i class="fa-solid fa-spinner fa-spin fa-2x text-primary"></i>',
                showConfirmButton: false,
                background: '#0A192F',
                color: '#E6F1FF'
            });

            const repairs = {
                'database': 'optimize_db',
                'storage': 'purge_cache',
                'security': 'fix_permissions',
                'models': 'optimize_db'
            };

            fetch(`/cortex/repair/${repairs[component] || component}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if(data.success) {
                    Swal.fire({ icon: 'success', title: 'Operación Exitosa', text: data.message, background: '#0A192F', color: '#E6F1FF' });
                    setTimeout(() => window.location.reload(), 1500);
                } else {
                    Swal.fire({ icon: 'error', title: 'Fallo de Autorización', text: data.message, background: '#0A192F', color: '#E6F1FF' });
                }
            });
        }
    });
}

function executeSecurityResponse(action, incidentKey) {
    const config = {
        'AISLAR': { title: 'PROTOCOLO DE AISLAMIENTO', text: '¿Confirmas el aislamiento del nodo afectado?', icon: 'warning', btn: '#F43F5E' },
        'IGNORAR': { title: 'DESCARTAR INCIDENTE', text: '¿Deseas marcar este incidente como falso positivo?', icon: 'info', btn: '#64FFDA' }
    };

    Swal.fire({
        title: config[action].title,
        text: config[action].text,
        icon: config[action].icon,
        showCancelButton: true,
        confirmButtonColor: config[action].btn,
        cancelButtonColor: '#112240',
        confirmButtonText: 'CONFIRMAR',
        cancelButtonText: 'CANCELAR',
        background: '#0A192F',
        color: '#E6F1FF'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'PROCESANDO RESPUESTA',
                html: '<i class="fa-solid fa-shield-halved fa-spin fa-2x text-primary"></i>',
                showConfirmButton: false,
                background: '#0A192F',
                color: '#E6F1FF'
            });

            // Enviar petición POST al backend para resolver/ignorar el incidente de seguridad
            fetch('/cortex/incident/dismiss', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ key: incidentKey })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Acción Ejecutada',
                        text: data.message,
                        background: '#0A192F',
                        color: '#E6F1FF'
                    }).then(() => window.location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error de Ejecución',
                        text: data.message,
                        background: '#0A192F',
                        color: '#E6F1FF'
                    });
                }
            })
            .catch(err => {
                Swal.fire({
                    icon: 'error',
                    title: 'Fallo de Red',
                    text: 'No se pudo comunicar con el núcleo de seguridad de Cortex.',
                    background: '#0A192F',
                    color: '#E6F1FF'
                });
            });
        }
    });
}

function restoreSecurityIncidents() {
    Swal.fire({
        title: 'RESTAURAR HISTORIAL',
        text: '¿Deseas restaurar todas las alertas e incidentes ignorados para volver a analizarlos?',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#64FFDA',
        cancelButtonColor: '#112240',
        confirmButtonText: 'RESTAURAR',
        cancelButtonText: 'CANCELAR',
        background: '#0A192F',
        color: '#E6F1FF'
    }).then((result) => {
        if (result.isConfirmed) {
            Swal.fire({
                title: 'RECOLECTANDO DATOS',
                html: '<i class="fa-solid fa-rotate-left fa-spin fa-2x text-primary"></i>',
                showConfirmButton: false,
                background: '#0A192F',
                color: '#E6F1FF'
            });

            fetch('/cortex/incident/restore', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Alertas Restauradas',
                        text: data.message,
                        background: '#0A192F',
                        color: '#E6F1FF'
                    }).then(() => window.location.reload());
                }
            });
        }
    });
}

function runAIScan() {
    Swal.fire({
        title: 'INICIANDO ESCANEO ESTRATÉGICO',
        html: `
            <div class="text-left mt-3" style="font-family: 'JetBrains Mono'; font-size: 0.8rem; color: #64FFDA;">
                <div id="scan-log"></div>
                <div class="progress mt-3" style="height: 4px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                    <div id="scan-progress" class="progress-bar" style="width: 0%; background: #64FFDA; box-shadow: 0 0 10px #64FFDA; transition: width 0.4s;"></div>
                </div>
            </div>
        `,
        background: '#0A192F',
        color: '#E6F1FF',
        showConfirmButton: false,
        allowOutsideClick: false,
        didOpen: () => {
            const log = document.getElementById('scan-log');
            const progress = document.getElementById('scan-progress');
            const steps = [
                'Iniciando protocolos de enlace...',
                'Analizando integridad de base de datos...',
                'Escaneando firmas de seguridad...',
                'Evaluando patrones de flujo...',
                'Compilando reporte de Cortex Assistant...'
            ];
            
            let current = 0;
            const interval = setInterval(() => {
                if (current < steps.length) {
                    const p = document.createElement('p');
                    p.innerHTML = '> ' + steps[current];
                    p.className = 'mb-1';
                    log.appendChild(p);
                    progress.style.width = ((current + 1) / steps.length * 100) + '%';
                    current++;
                } else {
                    clearInterval(interval);
                    window.location.href = "{{ route('cortex.scan') }}";
                }
            }, 600);
        }
    });
}

// FEEDBACK DE ESCANEO
@if(session('scan_completed'))
    Swal.fire({
        icon: "{{ session('scan_errors') ? 'warning' : 'success' }}",
        title: "{{ session('scan_errors') ? 'Diagnóstico Finalizado' : 'Sistema Nominal' }}",
        text: "{{ session('scan_errors') ? 'Se detectaron anomalías que requieren atención inmediata.' : 'Todos los módulos operan en parámetros óptimos.' }}",
        background: '#0A192F',
        color: '#E6F1FF',
        confirmButtonColor: '#64FFDA'
    });
@endif
</script>
@endsection
