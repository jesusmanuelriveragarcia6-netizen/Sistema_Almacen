@extends('layouts.app')

@section('title', 'Taller de Mantenimiento')

@section('content')
@php
// Agrupar por estado para los KPIs
$counts = ['Dañado'=>0, 'Mantenimiento'=>0, 'Revision'=>0, 'Perdido'=>0, 'Falla técnica'=>0];
foreach($herramientas as $h) {
    if(isset($counts[$h->estado])) $counts[$h->estado]++;
}

$colores = [
    'Dañado'       => ['bg'=>'rgba(239,68,68,.12)',  'border'=>'#EF4444', 'text'=>'#EF4444',  'icon'=>'fa-hammer',                'emoji'=>'🔨'],
    'Mantenimiento'=> ['bg'=>'rgba(245,158,11,.12)', 'border'=>'#F59E0B', 'text'=>'#F59E0B',  'icon'=>'fa-screwdriver-wrench',    'emoji'=>'🔧'],
    'Revision'     => ['bg'=>'rgba(56,189,248,.12)', 'border'=>'#38BDF8', 'text'=>'#38BDF8',  'icon'=>'fa-magnifying-glass',      'emoji'=>'🔍'],
    'Perdido'      => ['bg'=>'rgba(139,92,246,.12)', 'border'=>'#8B5CF6', 'text'=>'#8B5CF6',  'icon'=>'fa-circle-question',       'emoji'=>'❓'],
    'Falla técnica'=> ['bg'=>'rgba(239,68,68,.12)',  'border'=>'#EF4444', 'text'=>'#EF4444',  'icon'=>'fa-triangle-exclamation',  'emoji'=>'⚠️'],
];
@endphp

<style>
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(14px); }
    to   { opacity:1; transform:translateY(0); }
}
.mant-card { animation: fadeInUp .35s ease both; }
.mant-row:hover { background: rgba(255,255,255,.03); transition: background .2s; }
</style>

<!-- Header -->
<div class="d-flex mb-4" style="flex-wrap:wrap; gap:1rem; align-items:flex-end;">
    <div style="flex:1;">
        <h1 class="page-title" style="margin-bottom:0;">
            <i class="fa-solid fa-screwdriver-wrench" style="color:#F59E0B;"></i> Taller de Mantenimiento
        </h1>
        <p style="color:var(--text-muted); margin-top:.25rem; font-size:.9rem;">
            Herramientas retiradas del inventario activo por incidencias registradas.
        </p>
    </div>
    <a href="{{ route('incidencias.index') }}" class="btn" style="background:rgba(239,68,68,.1); border:1px solid #EF4444; color:#EF4444;">
        <i class="fa-solid fa-triangle-exclamation"></i> Registrar Incidencia
    </a>
</div>

<!-- Alertas flash -->
@if(session('exito') || request()->has('exito'))
<div class="alert mant-card" style="background:rgba(16,185,129,.1); color:#10B981; border:1px solid #10B981; margin-bottom:1.5rem;">
    <i class="fa-solid fa-check-circle"></i> Herramienta marcada como <strong>Reparada</strong> y devuelta al inventario activo.
</div>
@endif
@if(session('enviado') || request()->has('enviado'))
<div class="alert mant-card" style="background:rgba(56,189,248,.1); color:#38BDF8; border:1px solid #38BDF8; margin-bottom:1.5rem;">
    <i class="fa-solid fa-arrow-right"></i> Herramienta ingresada al taller correctamente.
</div>
@endif

<!-- KPIs -->
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:1rem; margin-bottom:2rem;">
@foreach($colores as $estado => $c)
@php $delay = array_search($estado, array_keys($colores)) * .07; @endphp
<div class="mant-card" style="background:{{ $c['bg'] }}; border:1px solid {{ $c['border'] }}; border-radius:12px; padding:1.1rem 1.25rem; display:flex; align-items:center; gap:1rem; animation-delay:{{ $delay }}s;">
    <span style="font-size:1.8rem;">{{ $c['emoji'] }}</span>
    <div>
        <div style="font-size:1.6rem; font-weight:700; color:{{ $c['text'] }}; line-height:1;">{{ $counts[$estado] }}</div>
        <div style="font-size:.78rem; color:var(--text-muted); margin-top:.15rem;">{{ $estado }}</div>
    </div>
</div>
@endforeach
</div>

<!-- Tabla principal -->
<div class="card mant-card" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; animation-delay:.15s;">

    @if($herramientas->isEmpty())
    <div style="text-align:center; padding:3.5rem; color:var(--text-muted);">
        <i class="fa-solid fa-circle-check" style="font-size:3rem; color:#10B981; opacity:.5; display:block; margin-bottom:1rem;"></i>
        <strong style="color:var(--text-main); font-size:1.05rem;">¡Todo en orden!</strong><br>
        <span style="font-size:.9rem; margin-top:.4rem; display:block;">No hay herramientas en el taller en este momento.</span>
    </div>
    @else
    <div class="table-container" style="overflow-x:auto;">
        <table class="table" style="width:100%; border-collapse:collapse;">
            <thead>
                <tr>
                    <th>Herramienta</th>
                    <th>Ubicación</th>
                    <th>Estado / Tipo</th>
                    <th>Descripción de la Incidencia</th>
                    <th style="text-align:right;">Acción</th>
                </tr>
            </thead>
            <tbody>
                @foreach($herramientas as $h)
                @php
                    $c = $colores[$h->estado] ?? ['bg'=>'rgba(136,146,176,.1)','border'=>'#8892B0','text'=>'#8892B0','emoji'=>'⚙️'];
                    $desc = !empty($h->descripcion) ? e($h->descripcion) : '<em style="color:var(--text-muted); font-size:.85rem;">Sin descripción registrada.</em>';
                @endphp
                <tr class="mant-row" style="border-bottom:1px solid var(--border-color);">
                    <td style="padding:1rem .75rem;">
                        <div style="font-weight:600; color:var(--primary-color);">{{ $h->codigo }}</div>
                        <div style="color:var(--text-main); margin-top:.15rem;">{{ $h->nombre }}</div>
                        <div style="color:var(--text-muted); font-size:.78rem; margin-top:.15rem;">
                            Stock total: {{ $h->stock_total }} | Disp: {{ $h->stock_disponible }}
                        </div>
                    </td>
                    <td style="padding:1rem .75rem; color:var(--text-muted); font-size:.9rem;">
                        {{ $h->ubicacion ?? '—' }}
                    </td>
                    <td style="padding:1rem .75rem;">
                        <span style="background:{{ $c['bg'] }}; border:1px solid {{ $c['border'] }}; color:{{ $c['text'] }}; padding:4px 10px; border-radius:6px; font-size:.82rem; font-weight:600; white-space:nowrap;">
                            {{ $c['emoji'] }} {{ $h->estado }}
                        </span>
                    </td>
                    <td style="padding:1rem .75rem; color:var(--text-muted); font-size:.88rem; max-width:260px;">
                        @if(!empty($h->descripcion))
                            {{ $h->descripcion }}
                        @else
                            <em style="color:var(--text-muted); font-size:.85rem;">Sin descripción registrada.</em>
                        @endif
                    </td>
                    <td style="padding:1rem .75rem; text-align:right;">
                        @if($h->estado !== 'Perdido' && Auth::user()->rol !== 'Supervisor')
                        <form action="{{ route('mantenimiento.reparar', $h->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Confirmas que esta herramienta fue revisada/reparada y puede volver al inventario activo?');">
                            @csrf
                            <button type="submit" class="btn" style="background:rgba(16,185,129,.1); color:#10B981; border:1px solid #10B981; padding:.45rem 1rem; font-size:.88rem; white-space:nowrap; cursor:pointer;">
                                <i class="fa-solid fa-check"></i> Marcar Reparada
                            </button>
                        </form>
                        @elseif($h->estado === 'Perdido')
                        <span style="color:var(--text-muted); font-size:.82rem; font-style:italic;">Sin retorno al inventario</span>
                        @else
                        <span style="color:var(--text-muted); font-size:.8rem;"><i class="fa-solid fa-eye"></i> Solo lectura</span>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif
</div>
@endsection
