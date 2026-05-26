@extends('layouts.app')

@section('title', 'Expediente del Trabajador')

@section('content')
@php
$vales_activos = $vales->whereIn('estado', ['Activo', 'Parcial'])->count();
$es_inactivo = ($trabajador->estado !== 'Activo');
$total_vales = $vales->count();
$volver_url = $es_inactivo ? route('trabajadores.inactivos') : route('trabajadores.index');
@endphp

<!-- Estilos de impresión (solo aplican al exportar a PDF / imprimir) -->
<style>
@media print {
    .sidebar, nav, .no-print, header, .btn, .action-btn,
    form[method="POST"], .page-actions, .nav-link { display: none !important; }

    body, .main-content, .wrapper, .content-area {
        background: #fff !important;
        color: #000 !important;
    }

    .print-header { display: block !important; }

    .card, .table-container {
        border: 1px solid #ccc !important;
        page-break-inside: avoid;
    }

    table th { background: #f0f0f0 !important; color: #000 !important; }
    table td, table th { border: 1px solid #ccc !important; color: #000 !important; }

    .badge-activo   { color: #16a34a !important; font-weight: bold; }
    .badge-cerrado  { color: #0ea5e9 !important; font-weight: bold; }
    .badge-parcial  { color: #d97706 !important; font-weight: bold; }
    .badge-inactivo { color: #dc2626 !important; font-weight: bold; }
}

.print-header { display: none; }
</style>

<!-- Cabecera para cuando se imprime -->
<div class="print-header" style="margin-bottom:1.5rem; border-bottom:2px solid #000; padding-bottom:1rem;">
    <h2 style="margin:0;">📋 Reporte de Trabajador</h2>
    <p style="margin:0.25rem 0 0;">Sistema de Gestión de Almacén — Fecha de impresión: {{ date('d/m/Y H:i') }}</p>
</div>

<!-- Barra de acciones -->
<div class="d-flex mb-4 no-print" style="flex-wrap:wrap; gap:1rem;">
    <div style="flex:1;">
        <h1 class="page-title" style="margin-bottom:0;">
            <i class="fa-solid fa-id-card mr-1"></i> Expediente del Trabajador
        </h1>
        <p style="color:var(--text-muted); margin-top:0.25rem; font-size:0.9rem;">
            Historial completo de actividad y responsabilidad en el almacén.
        </p>
    </div>
    <div style="display:flex; gap:0.75rem; align-items:flex-start;" class="page-actions">
        <a href="{{ $volver_url }}" class="btn" style="background:var(--surface-color); border:1px solid var(--border-color); color:var(--text-main);">
            <i class="fa-solid fa-arrow-left mr-1"></i> Volver
        </a>
        <button onclick="window.print()" class="btn btn-primary" style="background:linear-gradient(135deg,#0F766E,#0D9488);">
            <i class="fa-solid fa-file-pdf mr-1"></i> Exportar a PDF
        </button>
    </div>
</div>

<!-- KPIs superiores -->
<div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(180px,1fr)); gap:1.25rem; margin-bottom:2rem;">
    <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; text-align:center;">
        <div style="font-size:2rem; color:var(--primary-color); margin-bottom:0.25rem;">
            <i class="fa-solid fa-file-invoice"></i>
        </div>
        <div style="font-size:2rem; font-weight:700; color:var(--text-main);">{{ $total_vales }}</div>
        <div style="font-size:0.8rem; color:var(--text-muted);">Vales Emitidos</div>
    </div>
    <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; text-align:center;">
        <div style="font-size:2rem; color:#F59E0B; margin-bottom:0.25rem;">
            <i class="fa-solid fa-clock"></i>
        </div>
        <div style="font-size:2rem; font-weight:700; color:var(--text-main);">{{ $vales_activos }}</div>
        <div style="font-size:0.8rem; color:var(--text-muted);">Vales Activos Ahora</div>
    </div>
    <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; text-align:center;">
        <div style="font-size:2rem; color:{{ $es_inactivo ? '#EF4444' : '#10B981' }}; margin-bottom:0.25rem;">
            <i class="fa-solid fa-{{ $es_inactivo ? 'user-slash' : 'user-check' }}"></i>
        </div>
        <div style="font-size:1.2rem; font-weight:700; color:var(--text-main);">{{ $es_inactivo ? 'Inactivo' : 'Activo' }}</div>
        <div style="font-size:0.8rem; color:var(--text-muted);">Estado en el Sistema</div>
    </div>
    <div class="card" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:12px; padding:1.25rem; text-align:center;">
        <div style="font-size:2rem; color:#10B981; margin-bottom:0.25rem;">
            <i class="fa-solid fa-check-double"></i>
        </div>
        <div style="font-size:2rem; font-weight:700; color:var(--text-main);">{{ $total_vales - $vales_activos }}</div>
        <div style="font-size:0.8rem; color:var(--text-muted);">Vales Cerrados</div>
    </div>
</div>

<!-- Datos personales + Historial de Vales -->
<div style="display:flex; gap:1.5rem; flex-wrap:wrap; align-items:flex-start;">

    <!-- Tarjeta de Datos Personales -->
    <div class="card" style="flex:0 0 280px; background:var(--surface-color); border:1px solid var(--border-color); border-radius:12px; padding:1.5rem;">
        <div style="text-align:center; margin-bottom:1.25rem;">
            <div style="width:70px; height:70px; border-radius:50%; background:linear-gradient(135deg,#0D9488,#0F766E); display:inline-flex; align-items:center; justify-content:center; font-size:1.8rem; color:#fff; margin-bottom:0.75rem;">
                {{ mb_strtoupper(mb_substr($trabajador->nombre, 0, 1)) }}
            </div>
            <h3 style="margin:0; color:var(--text-main);">{{ $trabajador->nombre }} {{ $trabajador->apellidos }}</h3>
            <p style="margin:0.25rem 0 0; color:var(--text-muted); font-size:0.9rem;">{{ $trabajador->cargo ?: '—' }}</p>
        </div>

        <div style="border-top:1px solid var(--border-color); padding-top:1rem; display:flex; flex-direction:column; gap:0.75rem;">
            <div>
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">DNI</div>
                <div style="color:var(--text-main); font-weight:600;">{{ $trabajador->dni }}</div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Teléfono</div>
                <div style="color:var(--text-main);">{{ $trabajador->telefono ?: '—' }}</div>
            </div>
            <div>
                <div style="font-size:0.75rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:0.05em;">Estado</div>
                <div>
                    @if($es_inactivo)
                        <span style="color:#EF4444; background:rgba(239,68,68,0.1); padding:3px 8px; border-radius:4px; font-size:0.85rem;" class="badge-inactivo">
                            <i class="fa-solid fa-user-slash"></i> Ex-Trabajador
                        </span>
                    @else
                        <span style="color:#10B981; background:rgba(16,185,129,0.1); padding:3px 8px; border-radius:4px; font-size:0.85rem;" class="badge-activo">
                            <i class="fa-solid fa-user-check"></i> Activo
                        </span>
                    @endif
                </div>
            </div>
            @if($vales_activos > 0)
            <div style="background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.4); border-radius:8px; padding:0.75rem; margin-top:0.5rem;">
                <div style="color:#F59E0B; font-weight:600; font-size:0.9rem;">
                    <i class="fa-solid fa-triangle-exclamation"></i> Responsabilidad Activa
                </div>
                <div style="color:var(--text-muted); font-size:0.8rem; margin-top:0.25rem;">
                    Tiene {{ $vales_activos }} vale(s) con herramientas no devueltas.
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Historial de Vales -->
    <div style="flex:1; min-width:300px;">
        <h3 style="color:var(--text-main); margin-bottom:1rem;">
            <i class="fa-solid fa-history mr-1" style="color:var(--primary-color);"></i>
            Historial Completo de Vales
        </h3>

        @if($vales->isEmpty())
        <div style="text-align:center; padding:3rem; color:var(--text-muted); background:var(--surface-color); border-radius:12px; border:1px solid var(--border-color);">
            <i class="fa-solid fa-box-open" style="font-size:2.5rem; opacity:0.4; display:block; margin-bottom:0.75rem;"></i>
            Este trabajador no ha solicitado herramientas aún.
        </div>
        @else
        <div class="table-container">
            <table class="table" style="font-size:0.9rem;">
                <thead>
                    <tr>
                        <th>Código Vale</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th class="no-print">Comprobante</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($vales as $v)
                    <tr>
                        <td style="font-weight:600; color:var(--primary-color);">{{ $v->codigo_vale }}</td>
                        <td>{{ \Carbon\Carbon::parse($v->fecha_creacion)->format('d/m/Y H:i') }}</td>
                        <td>
                            @if($v->estado === 'Activo')
                                <span class="badge-activo" style="color:#F59E0B; background:rgba(245,158,11,0.1); padding:3px 8px; border-radius:4px; font-size:0.8rem;">
                                    <i class="fa-solid fa-clock"></i> Activo
                                </span>
                            @elseif($v->estado === 'Parcial')
                                <span class="badge-parcial" style="color:#F59E0B; background:rgba(245,158,11,0.1); padding:3px 8px; border-radius:4px; font-size:0.8rem;">
                                    <i class="fa-solid fa-circle-half-stroke"></i> Parcial
                                </span>
                            @else
                                <span class="badge-cerrado" style="color:#10B981; background:rgba(16,185,129,0.1); padding:3px 8px; border-radius:4px; font-size:0.8rem;">
                                    <i class="fa-solid fa-check"></i> Cerrado
                                </span>
                            @endif
                        </td>
                        <td class="no-print">
                            <a href="{{ url('vales/' . $v->id) }}" class="action-btn" title="Ver comprobante del vale" style="color:var(--primary-color);">
                                <i class="fa-solid fa-file-invoice"></i> Ver Vale
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>
</div>
@endsection
