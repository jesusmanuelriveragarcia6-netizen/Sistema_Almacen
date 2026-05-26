@props(['title', 'icon', 'priority' => 'normal'])

@php
    $priorityClass = match($priority) {
        'critical' => 'priority-critical',
        'high'     => 'priority-high',
        'medium'   => 'priority-medium',
        default    => 'priority-low',
    };
    
    $iconColor = match($priority) {
        'critical' => 'text-danger',
        'high'     => 'text-warning',
        'medium'   => 'text-info',
        default    => 'text-primary',
    };
@endphp

<div class="cortex-module {{ $priorityClass }}">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="d-flex align-items-center gap-3">
            <div class="icon-box" style="background: rgba(255,255,255,0.03); width: 56px; height: 56px; border-radius: 14px; display: flex; align-items: center; justify-content: center; border: 1px solid rgba(255,255,255,0.05);">
                <i class="fa-solid {{ $icon }} {{ $iconColor }}" style="font-size: 1.5rem;"></i>
            </div>
            <h4 class="m-0" style="font-size: 1.1rem; letter-spacing: 2px; font-weight: 800; color: #E6F1FF;">{{ strtoupper($title) }}</h4>
        </div>
        <div class="priority-badge">
            <span class="badge" style="font-size: 0.7rem; padding: 6px 12px; background: rgba(255,255,255,0.05); color: var(--text-muted); border: 1px solid rgba(255,255,255,0.05);">{{ strtoupper($priority) }}</span>
        </div>
    </div>
    
    <div class="module-body">
        {{ $slot }}
    </div>
</div>
