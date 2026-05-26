@extends('layouts.app')

@section('title', 'Reporte Temporal')

@section('content')
<div class="container-fluid">
    <div class="d-flex mb-4 d-print-none" style="align-items: center; justify-content: space-between;">
        <div>
            <h2 class="page-title" style="margin-bottom: 0.5rem;"><i class="fa-solid fa-calendar-days mr-1 text-success"></i> Reporte Temporal</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Filtrado de vales por rango de fechas y periodos.</p>
        </div>
        <div style="display: flex; gap: 1rem;">
            <a href="{{ route('reportes.index') }}" class="btn btn-secondary">
                <i class="fa-solid fa-arrow-left mr-1"></i> Volver
            </a>
            <button onclick="window.print()" class="btn btn-secondary" style="border-color: #10B981; color: #10B981; background: rgba(16, 185, 129, 0.1);">
                <i class="fa-solid fa-print mr-1"></i> Imprimir
            </button>
        </div>
    </div>

    <!-- Filtros -->
    <div class="table-container mb-4 d-print-none" style="padding: 1.5rem; background: var(--surface-color);">
        <form action="{{ route('reportes.temporal') }}" method="GET" style="display: flex; flex-wrap: wrap; gap: 1.5rem; align-items: flex-end;">
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.8rem; font-weight: 600;">FECHA INICIO</label>
                <input type="date" name="inicio" class="form-control" value="{{ $inicio ?? '' }}">
            </div>
            <div style="flex: 1; min-width: 150px;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.8rem; font-weight: 600;">FECHA FIN</label>
                <input type="date" name="fin" class="form-control" value="{{ $fin ?? '' }}">
            </div>
            <div style="flex: 0 0 120px;">
                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem;">
                    <i class="fa-solid fa-filter"></i>
                </button>
            </div>
            <div style="flex: 2; min-width: 300px;">
                <label style="display: block; margin-bottom: 0.5rem; color: var(--text-muted); font-size: 0.8rem; font-weight: 600;">FILTRAR POR DÍAS</label>
                <div style="display: flex; gap: 0.5rem; align-items: center;">
                    <a href="?periodo=semana" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.5rem 1rem;">Semana</a>
                    <a href="?periodo=mes" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.5rem 1rem;">Mes</a>
                    <div style="display: flex; background: #112240; border-radius: 8px; border: 1px solid var(--border-color); padding: 2px;">
                        <input type="number" name="dias" placeholder="Días..." style="width: 70px; background: transparent; border: none; color: white; padding: 0.4rem; font-size: 0.8rem;" min="1">
                        <button type="submit" class="btn btn-primary" style="padding: 0.4rem 0.8rem; font-size: 0.75rem; width: auto;">Ir</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Encabezado de impresión -->
    <div class="d-none d-print-block mb-4 text-center" style="color: white; border-bottom: 2px solid #10B981; padding-bottom: 1rem;">
        <h1 style="color: #10B981;">ALMACÉN INTELIGENTE</h1>
        <h2>Reporte Temporal de Vales de Salida</h2>
        <p>Periodo: {{ date('d/m/Y', strtotime($inicio)) }} al {{ date('d/m/Y', strtotime($fin)) }}</p>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Vale</th>
                    <th>Fecha / Hora</th>
                    <th>Trabajador</th>
                    <th style="text-align: center;">Items</th>
                    <th style="text-align: center;">Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @if(empty($reporte))
                <tr>
                    <td colspan="6" style="text-align: center; padding: 3rem; color: var(--text-muted);">
                        No se encontraron registros en el periodo seleccionado.
                    </td>
                </tr>
                @else
                    @foreach($reporte as $row)
                    <tr>
                        <td><span style="font-weight: 600; color: var(--primary-color);">{{ $row->codigo_vale }}</span></td>
                        <td>{{ \Carbon\Carbon::parse($row->fecha_creacion)->format('d/m/Y H:i') }}</td>
                        <td>{{ $row->trabajador_nombre }} {{ $row->trabajador_apellidos }}</td>
                        <td style="text-align: center;">
                            <span class="badge" style="background: rgba(100, 255, 218, 0.1); color: var(--primary-color);">
                                {{ $row->items }}
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge {{ $row->estado }}">
                                {{ $row->estado }}
                            </span>
                        </td>
                        <td style="text-align: right;">
                            <a href="{{ route('vales.show', $row->id) }}" class="action-btn d-print-none">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                    @endforeach
                @endif
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
