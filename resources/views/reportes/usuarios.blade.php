@extends('layouts.app')

@section('title', 'Reporte por Usuario')

@section('content')
<div class="container-fluid">
    <div class="d-flex mb-4 d-print-none" style="align-items: center; justify-content: space-between;">
        <div>
            <h2 class="page-title" style="margin-bottom: 0.5rem;"><i class="fa-solid fa-user-tag mr-1 text-primary"></i> Reporte por Trabajador</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Préstamos totales realizados por cada trabajador.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left mr-1"></i> Volver
            </a>
            <button onclick="window.print()" class="btn btn-secondary">
                <i class="fa-solid fa-print mr-1"></i> Imprimir Reporte
            </button>
        </div>
    </div>

    <!-- Encabezado de impresión -->
    <div class="d-none d-print-block mb-4 text-center" style="color: white; border-bottom: 2px solid #64FFDA; padding-bottom: 1rem;">
        <h1 style="color: #64FFDA;">ALMACÉN INTELIGENTE</h1>
        <h2>Reporte de Herramientas por Trabajador</h2>
        <p>Fecha: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Trabajador</th>
                    <th>DNI</th>
                    <th style="text-align: center;">Total Vales</th>
                    <th style="text-align: center;">Total Herramientas</th>
                    <th style="text-align: right;">Sanciones Acumuladas</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte as $row)
                <tr>
                    <td>
                        <div style="font-weight: 600; color: var(--primary-color);">{{ $row->nombre }} {{ $row->apellidos }}</div>
                    </td>
                    <td><code>{{ $row->dni }}</code></td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: rgba(100, 255, 218, 0.1); color: var(--primary-color); border: 1px solid rgba(100, 255, 218, 0.2);">
                            {{ $row->total_vales }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span class="badge" style="background: rgba(56, 189, 248, 0.1); color: #38BDF8; border: 1px solid rgba(56, 189, 248, 0.2);">
                            {{ $row->total_herramientas ?? 0 }}
                        </span>
                    </td>
                    <td style="text-align: right; font-weight: bold; color: {{ $row->total_sanciones > 0 ? '#EF4444' : 'var(--text-muted)' }};">
                        S/ {{ number_format($row->total_sanciones ?: 0, 2) }}
                    </td>
                    <td style="text-align: right;">
                        <a href="{{ route('trabajadores.show', $row->id) }}" class="action-btn d-print-none" title="Ver Trabajador">
                            <i class="fa-solid fa-eye"></i>
                        </a>
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
