@extends('layouts.app')

@section('title', 'Control Logístico Neural')

@section('content')
<div class="logistics-blueprint">
    <!-- HEADER ESTRATÉGICO -->
    <div class="d-flex justify-content-between align-items-center mb-5 flex-wrap gap-4">
        <div>
            <h1 class="blueprint-title m-0">CENTRO LOGÍSTICO <span class="text-primary-glow-text">CORTEX</span></h1>
            <p class="text-muted m-0 mt-1">Mapa interactivo de activos físicos y distribución de carga.</p>
        </div>
        <div class="blueprint-stats">
            <div class="stat-box">
                <span class="label">NODOS ACTIVOS</span>
                <span class="value">{{ $almacenes->count() }}</span>
            </div>
        </div>
    </div>

    @if($almacenes->isEmpty())
        <div class="empty-blueprint">
            <i class="fa-solid fa-microchip mb-4" style="font-size: 4rem; color: rgba(100, 255, 218, 0.1);"></i>
            <h3>SIN NODOS CONFIGURADOS</h3>
            <p class="text-muted">La red logística no ha detectado almacenes operativos.</p>
            <a href="{{ route('almacenes.index') }}" class="btn btn-primary mt-3 font-weight-bold px-4">INICIALIZAR RED</a>
        </div>
    @else
        <!-- GRID DE ALMACENES (BLUEPRINT TILES) -->
        <div class="row">
            @foreach($almacenes as $alm)
            @php
                $total_items = $alm->herramientas->count();
                $occupancy = min(($total_items / 50) * 100, 100);
                $secciones = $alm->herramientas->groupBy(function($h) {
                    return $h->categoria ? $h->categoria->nombre : 'General';
                });
            @endphp
            <div class="col-xl-6 col-md-6 mb-4">
                <div class="warehouse-tile">
                    <div class="tile-header">
                        <div class="d-flex align-items-center gap-3">
                            <div class="tile-icon">
                                <i class="fa-solid fa-server"></i>
                            </div>
                            <div>
                                <h3 class="m-0 h5 text-white font-weight-bold">{{ strtoupper($alm->nombre) }}</h3>
                                <span class="text-primary small font-weight-bold">NODO ID: {{ str_pad($alm->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                        </div>
                        <div class="tile-badge">
                            <span class="node-dot pulse"></span>
                            ONLINE
                        </div>
                    </div>

                    <div class="tile-body my-4">
                        <div class="occupancy-meter">
                            <div class="d-flex justify-content-between mb-2 small font-weight-bold">
                                <span class="text-muted">CARGA DE ACTIVOS</span>
                                <span class="text-white">{{ $total_items }} UNIDADES</span>
                            </div>
                            <div class="progress" style="height: 8px; background: rgba(255,255,255,0.05); border-radius: 10px;">
                                <div class="progress-bar" style="width: {{ $occupancy }}%; background: #64FFDA; box-shadow: 0 0 10px #64FFDA; border-radius: 10px;"></div>
                            </div>
                        </div>
                    </div>

                    <div class="tile-footer pt-3" style="border-top: 1px solid rgba(255,255,255,0.05);">
                        <button class="btn-tile-action" type="button" data-toggle="modal" data-target="#modal-warehouse-{{ $alm->id }}">
                            INSPECCIONAR ACTIVOS <i class="fa-solid fa-arrow-right ml-2 text-primary"></i>
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- MODALES DE ALMACENES (Fuera del grid principal para evitar problemas de z-index y rendering) -->
        @foreach($almacenes as $alm)
        @php
            $total_items = $alm->herramientas->count();
            $secciones = $alm->herramientas->groupBy(function($h) {
                return $h->categoria ? $h->categoria->nombre : 'General';
            });
        @endphp
            <!-- MODAL PREMIUM DETALLE DE INVENTARIO -->
            <div class="modal fade cortex-modal" id="modal-warehouse-{{ $alm->id }}" tabindex="-1" role="dialog" aria-labelledby="modalLabel-{{ $alm->id }}" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered" role="document">
                    <div class="modal-content cortex-modal-content">
                        <div class="modal-header border-0 pb-3 d-flex justify-content-between align-items-start" style="border-bottom: 1px solid rgba(100,255,218,0.1) !important;">
                            <div class="d-flex align-items-center gap-3">
                                <div class="modal-icon">
                                    <i class="fa-solid fa-warehouse text-primary"></i>
                                </div>
                                <div>
                                    <h4 class="modal-title text-white font-weight-bold mb-1" id="modalLabel-{{ $alm->id }}">{{ strtoupper($alm->nombre) }}</h4>
                                    <span class="text-primary small font-weight-bold" style="letter-spacing: 1px;">NODO ID: {{ str_pad($alm->id, 3, '0', STR_PAD_LEFT) }}</span>
                                </div>
                            </div>
                            <button type="button" class="cortex-close-btn" data-dismiss="modal" aria-label="Close" title="Cerrar Blueprint">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                        
                        <div class="modal-body py-4" style="max-height: 70vh; overflow-y: auto;">
                            <div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded" style="background: rgba(100,255,218,0.03); border: 1px solid rgba(100,255,218,0.1);">
                                <div class="small">
                                    <span class="text-muted mr-2">TOTAL CATEGORÍAS:</span>
                                    <span class="text-white font-weight-bold">{{ $secciones->count() }}</span>
                                </div>
                                <div class="small">
                                    <span class="text-muted mr-2">TOTAL ACTIVOS:</span>
                                    <span class="text-white font-weight-bold">{{ $total_items }} UNIDADES</span>
                                </div>
                            </div>

                            @forelse($secciones as $nombreSeccion => $items)
                                <div class="modal-section mb-4">
                                    <div class="modal-section-title mb-3 d-flex align-items-center">
                                        <span class="section-title-line"></span>
                                        <span class="px-3 py-1 rounded-pill" style="background: rgba(100, 255, 218, 0.1); color: #64FFDA; font-size: 0.75rem; font-weight: 800; letter-spacing: 1.5px; position: relative; z-index: 2;">
                                            <i class="fa-solid fa-folder-tree mr-2"></i>{{ strtoupper($nombreSeccion) }}
                                        </span>
                                    </div>
                                    
                                    <div class="modal-grid">
                                        @foreach($items as $item)
                                        @php
                                            $hasPng = file_exists(public_path('img/herramientas/' . $item->id . '.png'));
                                            $hasSvg = file_exists(public_path('img/herramientas/' . $item->id . '.svg'));
                                            $stockPct = $item->stock_total > 0 ? ($item->stock_disponible / $item->stock_total) * 100 : 0;
                                            $stockColor = $stockPct > 50 ? '#64ffda' : ($stockPct > 20 ? '#ffcc00' : '#ff4d4d');
                                        @endphp
                                            <a href="{{ route('herramientas.show', $item->id) }}" class="modal-item text-decoration-none">
                                                {{-- Imagen + nombre --}}
                                                <div class="d-flex align-items-center gap-3 mb-3">
                                                    <div class="modal-item-img">
                                                        @if($hasPng)
                                                            <img src="/img/herramientas/{{ $item->id }}.png" alt="{{ $item->nombre }}">
                                                        @elseif($hasSvg)
                                                            <img src="/img/herramientas/{{ $item->id }}.svg" alt="{{ $item->nombre }}" style="object-fit:contain;padding:4px;">
                                                        @else
                                                            <i class="fa-solid fa-screwdriver-wrench" style="font-size:1.4rem;color:rgba(100,255,218,0.3);"></i>
                                                        @endif
                                                    </div>
                                                    <div style="min-width:0; flex:1; display:flex; flex-direction:column; gap:4px;">
                                                        <div class="item-name font-weight-bold" style="white-space:normal; word-break:break-word; line-height: 1.2;">{{ $item->nombre }}</div>
                                                        <div><span class="item-code">{{ $item->codigo }}</span></div>
                                                    </div>
                                                </div>

                                                {{-- Stock bar --}}
                                                <div class="mb-3">
                                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                                        <small class="text-muted" style="font-size:0.68rem;letter-spacing:0.5px;">STOCK</small>
                                                        <span class="font-weight-bold" style="color:{{ $stockColor }};font-size:0.85rem;">
                                                            {{ $item->stock_disponible }} <span style="color:#8892b0;font-size:0.7rem;font-weight:400;">/ {{ $item->stock_total }}</span>
                                                        </span>
                                                    </div>
                                                    <div class="progress" style="height:4px;background:rgba(255,255,255,0.05);border-radius:10px;">
                                                        <div class="progress-bar" style="width:{{ $stockPct }}%;background:{{ $stockColor }};box-shadow:0 0 8px {{ $stockColor }};border-radius:10px;"></div>
                                                    </div>
                                                </div>

                                                {{-- Footer: ubicación + estado --}}
                                                <div class="d-flex justify-content-between align-items-center pt-2" style="border-top:1px solid rgba(255,255,255,0.05);">
                                                    <div class="d-flex align-items-center text-muted" style="font-size:0.78rem;gap:4px;">
                                                        <i class="fa-solid fa-location-dot" style="color:#64ffda;font-size:0.7rem;"></i>
                                                        <span>{{ $item->ubicacion ?: 'Sin Ubicación' }}</span>
                                                    </div>
                                                    <span class="item-estado-badge {{ $item->estado === 'Disponible' ? 'estado-ok' : 'estado-warn' }}">
                                                        {{ $item->estado }}
                                                    </span>
                                                </div>
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center p-5">
                                    <i class="fa-solid fa-box-open text-muted mb-3" style="font-size: 3rem; opacity: 0.2;"></i>
                                    <p class="text-muted">Este almacén no contiene herramientas registradas.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-outline-secondary px-4 font-weight-bold" data-dismiss="modal" style="border-radius: 8px; border: 1px solid rgba(255,255,255,0.15); color: #8892B0;">CERRAR BLUEPRINT</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>

<style>
.logistics-blueprint { padding: 1rem; }
.blueprint-title { font-size: 2.2rem; font-weight: 900; color: #CCD6F6; letter-spacing: -0.5px; }
.text-primary-glow-text {
    color: #64FFDA;
    text-shadow: 0 0 10px rgba(100, 255, 218, 0.3);
}
.blueprint-stats {
    background: rgba(100, 255, 218, 0.05);
    border: 1px solid rgba(100, 255, 218, 0.1);
    padding: 0.5rem 1.5rem;
    border-radius: 12px;
}
.stat-box .label { font-size: 0.7rem; color: #8892B0; display: block; font-weight: 800; letter-spacing: 1px; }
.stat-box .value { font-size: 1.5rem; color: #64FFDA; font-weight: 900; }

.warehouse-tile {
    background: rgba(16, 25, 41, 0.6);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(100, 255, 218, 0.1);
    border-radius: 20px;
    padding: 2rem;
    transition: all 0.3s ease;
}

.warehouse-tile:hover {
    border-color: rgba(100, 255, 218, 0.3);
    box-shadow: 0 10px 30px rgba(0,0,0,0.3), 0 0 20px rgba(100, 255, 218, 0.05);
    transform: translateY(-2px);
}

.tile-header { display: flex; justify-content: space-between; align-items: flex-start; }
.tile-icon { width: 50px; height: 50px; background: rgba(100, 255, 218, 0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: #64FFDA; font-size: 1.5rem; }
.tile-badge {
    background: rgba(100, 255, 218, 0.1);
    color: #64FFDA;
    font-size: 0.65rem;
    font-weight: 900;
    padding: 6px 12px;
    border-radius: 6px;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    gap: 6px;
}

.node-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #64FFDA;
    box-shadow: 0 0 10px #64FFDA;
}

.node-dot.pulse { animation: node-dot-pulse 2s infinite; }

@keyframes node-dot-pulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.5); opacity: 0.5; }
    100% { transform: scale(1); opacity: 1; }
}

.btn-tile-action {
    background: transparent;
    border: none;
    color: #E6F1FF;
    font-weight: 800;
    font-size: 0.85rem;
    letter-spacing: 1px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s;
    width: 100%;
    cursor: pointer;
}

.btn-tile-action:hover {
    color: #64FFDA;
    text-shadow: 0 0 8px rgba(100, 255, 218, 0.4);
}

.empty-blueprint { padding: 5rem; text-align: center; background: rgba(16, 25, 41, 0.4); border-radius: 24px; border: 2px dashed rgba(100, 255, 218, 0.1); }

/* BOTÓN CERRAR MODAL CORTEX */
.cortex-close-btn {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    color: #CCD6F6;
    border-radius: 8px;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    cursor: pointer;
    font-size: 1.2rem;
}

.cortex-close-btn:hover {
    background: rgba(244, 63, 94, 0.15);
    color: #F43F5E;
    border-color: rgba(244, 63, 94, 0.3);
    transform: scale(1.05);
}

/* ESTILOS PARA MODALES PREMIUM CORTEX */
.cortex-modal-blur {
    background: rgba(5, 13, 26, 0.85) !important;
    backdrop-filter: blur(10px);
}

.cortex-modal-content {
    background: rgba(10, 25, 47, 0.98) !important;
    border: 1px solid rgba(100, 255, 218, 0.2) !important;
    border-radius: 20px !important;
    box-shadow: 0 20px 50px rgba(0,0,0,0.8), 0 0 40px rgba(100, 255, 218, 0.1) !important;
    backdrop-filter: blur(20px);
}

.modal-icon {
    width: 45px;
    height: 45px;
    background: rgba(100, 255, 218, 0.1);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.modal-section-title {
    position: relative;
    width: 100%;
}

.section-title-line {
    position: absolute;
    left: 0;
    top: 50%;
    width: 100%;
    height: 1px;
    background: linear-gradient(90deg, rgba(100, 255, 218, 0.2) 0%, transparent 100%);
    z-index: 1;
}

.modal-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
    gap: 1rem;
}

.modal-item {
    background: rgba(17, 34, 64, 0.5);
    border: 1px solid rgba(100, 255, 218, 0.08);
    border-radius: 14px;
    padding: 1rem;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
}

.modal-item:hover {
    background: rgba(17, 34, 64, 0.8);
    border-color: rgba(100, 255, 218, 0.3);
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.4), 0 0 15px rgba(100,255,218,0.05);
}

.modal-item-img {
    width: 52px;
    height: 52px;
    flex-shrink: 0;
    border-radius: 10px;
    background: linear-gradient(135deg, #112240, #0a192f);
    border: 1px solid rgba(100,255,218,0.15);
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.modal-item-img img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.item-name {
    font-size: 0.9rem;
    color: #CCD6F6;
    line-height: 1.3;
    margin: 0;
}

.item-code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.65rem;
    letter-spacing: 0.8px;
    color: #64ffda;
    background: rgba(100,255,218,0.08);
    padding: 3px 6px;
    border-radius: 4px;
    display: inline-block;
    border: 1px solid rgba(100,255,218,0.2);
}

.item-estado-badge {
    font-size: 0.62rem;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 4px;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}
.estado-ok  { background: rgba(100,255,218,0.12); color:#64ffda; border:1px solid rgba(100,255,218,0.25); }
.estado-warn{ background: rgba(255,77,77,0.12);   color:#ff4d4d; border:1px solid rgba(255,77,77,0.25); }

.bg-primary-glow {
    background: rgba(100, 255, 218, 0.15) !important;
    color: #64FFDA !important;
    border: 1px solid rgba(100, 255, 218, 0.3);
    box-shadow: 0 0 10px rgba(100, 255, 218, 0.1);
}

.bg-danger-glow {
    background: rgba(244, 63, 94, 0.15) !important;
    color: #F43F5E !important;
    border: 1px solid rgba(244, 63, 94, 0.3);
    box-shadow: 0 0 10px rgba(244, 63, 94, 0.1);
}

/* Custom Scrollbar for Modal Body */
.modal-body::-webkit-scrollbar {
    width: 6px;
}
.modal-body::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.02);
}
.modal-body::-webkit-scrollbar-thumb {
    background: rgba(100, 255, 218, 0.2);
    border-radius: 3px;
}
.modal-body::-webkit-scrollbar-thumb:hover {
    background: rgba(100, 255, 218, 0.4);
}
</style>
@endsection
