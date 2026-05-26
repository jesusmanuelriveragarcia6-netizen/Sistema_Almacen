@extends('layouts.app')

@section('title', 'Reporte por Personal del Sistema')

@section('content')
<div class="container-fluid">
    <div class="d-flex mb-4 d-print-none" style="align-items: center; justify-content: space-between;">
        <div>
            <h2 class="page-title" style="margin-bottom: 0.5rem;"><i class="fa-solid fa-user-gear mr-1 text-primary"></i> Reporte por Personal</h2>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Préstamos y herramientas procesadas por cada usuario del sistema.</p>
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
        <h2>Actividad de Personal del Sistema</h2>
        <p>Fecha: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Usuario</th>
                    <th>Nombre</th>
                    <th>Rol</th>
                    <th style="text-align: center;">Vales Registrados</th>
                    <th style="text-align: center;">Herramientas Procesadas</th>
                </tr>
            </thead>
            <tbody>
                @foreach($reporte as $row)
                <tr>
                    <td><code>{{ $row->usuario }}</code></td>
                    <td>
                        <div style="font-weight: 600; color: var(--text-main);">{{ $row->nombre }}</div>
                    </td>
                    <td>
                        <span class="badge" style="background: rgba(100, 255, 218, 0.1); color: var(--primary-color);">
                            {{ $row->rol }}
                        </span>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-weight: bold; font-size: 1.1rem; color: #38BDF8;">{{ $row->vales_registrados }}</span>
                    </td>
                    <td style="text-align: center;">
                        <span style="font-weight: bold; font-size: 1.1rem; color: #10B981;">{{ $row->herramientas_procesadas ?? 0 }}</span>
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
