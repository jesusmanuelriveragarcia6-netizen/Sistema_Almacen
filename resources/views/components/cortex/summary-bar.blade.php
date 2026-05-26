@props(['insights'])

<div class="cortex-summary-bar mb-5" onclick="openCortexAssistant()" style="cursor: pointer;">
    <div class="d-flex align-items-center gap-4">
        <div class="summary-icon">
            <i class="fa-solid fa-brain-circuit text-primary" style="font-size: 1.5rem;"></i>
        </div>
        <div class="summary-text">
            <span class="d-block text-white font-weight-bold" style="font-size: 0.9rem;">Cortex Assistant: <span class="text-muted font-weight-normal">Supervisión Activa</span></span>
            <p class="m-0 text-muted small">{{ $insights['summary'] }}</p>
        </div>
    </div>
    <div class="summary-metrics d-flex gap-4 border-left border-dark pl-5">
        <div class="metric-item">
            <small class="text-muted d-block uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">Alertas</small>
            <span class="{{ count($insights['security']['alerts']) > 0 ? 'text-danger' : 'text-success' }} font-weight-bold">{{ count($insights['security']['alerts']) }}</span>
        </div>
        <div class="metric-item">
            <small class="text-muted d-block uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">Recomendaciones</small>
            <span class="text-primary font-weight-bold">{{ count($insights['recommendations']) }}</span>
        </div>
        <div class="metric-item">
            <small class="text-muted d-block uppercase" style="font-size: 0.6rem; letter-spacing: 1px;">Estado</small>
            <span class="text-success font-weight-bold">Óptimo</span>
        </div>
    </div>
    <div class="summary-action">
        <button class="btn btn-sm btn-outline-primary" style="border-radius: 20px; font-size: 0.7rem; font-weight: 800; padding: 4px 15px;">
            ABRIR CONSOLA <i class="fa-solid fa-chevron-right ml-1"></i>
        </button>
    </div>
</div>
