@props(['label', 'status' => null, 'value' => null])

@php
    $dotClass = match($status) {
        'online', 'secure', 'pass' => 'text-success',
        'warning' => 'text-warning',
        'critical', 'fail', 'hacking_detected' => 'text-danger',
        default => 'text-primary'
    };

    $dotColor = match($status) {
        'online', 'secure', 'pass' => '#64FFDA',
        'warning' => '#F59E0B',
        'critical', 'fail', 'hacking_detected' => '#F43F5E',
        default => '#64FFDA'
    };
@endphp

<div class="status-node">
    <div class="node-dot pulse" style="background: {{ $dotColor }}; box-shadow: 0 0 10px {{ $dotColor }};"></div>
    <div class="d-flex flex-column" style="line-height: 1;">
        <span class="text-muted" style="font-size: 0.6rem; text-transform: uppercase; letter-spacing: 0.5px;">{{ $label }}</span>
        <span style="font-size: 0.75rem; font-weight: 700; color: #E6F1FF;">{{ $value ?? strtoupper($status) }}</span>
    </div>
</div>
