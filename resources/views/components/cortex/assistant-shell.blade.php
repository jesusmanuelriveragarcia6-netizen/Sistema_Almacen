<div class="cortex-container m-0 border-0" style="background: transparent;">
    <div class="d-flex justify-content-between align-items-center mb-5 px-5 pt-5">
        <div class="cortex-header">
            <span class="ai-badge mb-2 d-inline-block">Neural Analysis Console</span>
            <h2 class="m-0 text-white" style="font-weight: 800; font-size: 2.2rem; letter-spacing: -1px;">
                Cortex <span class="text-primary">Assistant</span>
            </h2>
        </div>
        <div class="cortex-nodes d-flex gap-3">
            <x-cortex.status-node label="System Health" status="online" />
            <x-cortex.status-node label="Security" :status="$attributes->get('security-status', 'secure')" />
        </div>
    </div>

    <div class="px-5 pb-5">
        {{ $slot }}
    </div>

    <div class="cortex-footer mt-5 px-4 d-flex justify-content-between align-items-center">
        <div class="system-meta small text-muted">
            <i class="fa-solid fa-microchip mr-1"></i> Engine: v3.2.0-NEURAL | Latency: 12ms
        </div>
        <div class="small text-muted" style="opacity: 0.5;">
            Cortex Intelligence Core © 2026
        </div>
    </div>
</div>
