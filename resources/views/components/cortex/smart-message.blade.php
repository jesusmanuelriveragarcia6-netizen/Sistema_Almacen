@props(['message', 'priority' => 'info'])

<div class="cortex-neural-core mb-4 {{ $priority === 'critical' ? 'priority-critical-pulse' : '' }}">
    <div class="row align-items-center">
        <div class="col-auto">
            <div class="neural-avatar">
                <div class="neural-ring"></div>
                <div class="neural-orb"></div>
            </div>
        </div>
        <div class="col">
            <div class="message-content pr-5">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="badge {{ $priority === 'critical' ? 'bg-danger' : 'bg-primary' }} p-1 px-3" style="font-size: 0.75rem; letter-spacing: 2px;">NEURAL CORE ALPHA</span>
                    <span class="text-muted small" style="font-family: 'JetBrains Mono';">Supervisión en Tiempo Real</span>
                </div>
                <h3 class="m-0" style="font-size: 1.8rem; line-height: 1.5; color: #E6F1FF; font-weight: 400; letter-spacing: -0.5px;">
                    {!! $message !!}
                </h3>
            </div>
        </div>
        @if($priority === 'critical')
        <div class="col-auto pr-4">
            <div class="d-flex flex-column align-items-center text-danger">
                <i class="fa-solid fa-triangle-exclamation fa-beat mb-2" style="font-size: 2rem;"></i>
                <span class="small font-weight-bold">ACCIÓN REQUERIDA</span>
            </div>
        </div>
        @endif
    </div>
</div>
