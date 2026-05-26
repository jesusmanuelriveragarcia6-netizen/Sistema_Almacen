@extends('layouts.app')

@section('title', 'Reporte de Herramientas')

@section('content')
<div class="container-fluid">
    <div class="d-flex mb-4 d-print-none" style="align-items: center; justify-content: space-between;">
        <div>
            <h2 class="page-title" style="margin-bottom: 0.5rem;"><i class="fa-solid fa-screwdriver-wrench mr-1 text-info"></i> Reporte de Herramientas</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Análisis de demanda e historial de mantenimiento por equipo.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left mr-1"></i> Volver
            </a>
            <button onclick="window.print()" class="btn btn-secondary" style="border-color: #38BDF8; color: #38BDF8; background: rgba(56, 189, 248, 0.1);">
                <i class="fa-solid fa-print mr-1"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- Encabezado de impresión -->
    <div class="d-none d-print-block mb-4 text-center" style="color: white; border-bottom: 2px solid #38BDF8; padding-bottom: 1rem;">
        <h1 style="color: #38BDF8;">ALMACÉN INTELIGENTE</h1>
        <h2>Demanda y Mantenimiento de Herramientas</h2>
        <p>Fecha: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Herramienta</th>
                    <th>Código</th>
                    <th style="text-align: center;">Veces Prestada</th>
                    <th style="text-align: center;">Mantenimientos</th>
                    <th style="text-align: center;">Incidencias</th>
                    <th style="text-align: right;">Gasto Sanciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte as $row)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: #E6F1FF;">{{ $row->nombre }}</div>
                    </td>
                    <td><code>{{ $row->codigo }}</code></td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: rgba(100, 255, 218, 0.1); color: var(--primary-color);">
                            {{ $row->veces_prestada }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: rgba(56, 189, 248, 0.1); color: #38BDF8;">
                            {{ $row->mantenimientos_realizados }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: {{ $row->total_incidencias > 3 ? 'rgba(239, 68, 68, 0.1)' : 'rgba(245, 158, 11, 0.1)' }}; color: {{ $row->total_incidencias > 3 ? '#EF4444' : '#F59E0B' }};">
                            {{ $row->total_incidencias }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: 700; color: #38BDF8;">
                        S/ {{ number_format($row->costo_mantenimiento ?? 0, 2) }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
.d-none { display: none; }
@media print {
    body { background: white !important; color: black !important; }
    .sidebar, .main-header, .d-print-none { display: none !important; }
    .main-content { margin-left: 0 !important; width: 100% !important; padding: 0 !important; }
    .page-content { padding: 1cm !important; }
    .table-container { border: none !important; box-shadow: none !important; background: transparent !important; }
    .table th { background: #eee !important; color: black !important; border-bottom: 2px solid black !important; }
    .table td { color: black !important; border-bottom: 1px solid #ccc !important; }
    .badge { border: 1px solid #000 !important; color: black !important; background: transparent !important; }
    .d-print-block { display: block !important; }
    h1, h2 { color: black !important; }
}
</style>
@endsection
