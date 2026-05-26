@extends('layouts.app')

@section('title', 'Centro de Reportes')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <h2 class="page-title"><i class="fa-solid fa-file-chart-pie mr-1"></i> Centro de Reportes</h2>
            <p style="color: var(--text-muted); margin-top: -1.5rem; margin-bottom: 2rem;">Seleccione el tipo de reporte que desea generar.</p>
        </div>
    </div>

    <div class="stats-grid">
        <!-- Reporte por Personal del Sistema -->
        <a href="{{ route('reportes.personal') }}" class="stat-card" style="text-decoration: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 3rem 1.5rem;">
            <div class="stat-icon" style="background: rgba(100, 255, 218, 0.1); color: var(--primary-color); width: 80px; height: 80px; border-radius: 50%; font-size: 2.5rem; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-user-gear"></i>
            </div>
            <div class="stat-details">
                <p style="font-size: 1.25rem; margin-bottom: 0.5rem; color: var(--primary-color);">Por Personal</p>
                <h3 style="color: var(--text-muted); font-weight: 400; text-transform: none; font-size: 0.9rem;">Préstamos procesados por cada administrador/almacenero.</h3>
            </div>
        </a>

        <!-- Reporte por Trabajador -->
        <a href="{{ route('reportes.usuarios') }}" class="stat-card" style="text-decoration: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 3rem 1.5rem;">
            <div class="stat-icon" style="background: rgba(245, 158, 11, 0.1); color: #F59E0B; width: 80px; height: 80px; border-radius: 50%; font-size: 2.5rem; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-user-tag"></i>
            </div>
            <div class="stat-details">
                <p style="font-size: 1.25rem; margin-bottom: 0.5rem; color: #F59E0B;">Por Trabajador</p>
                <h3 style="color: var(--text-muted); font-weight: 400; text-transform: none; font-size: 0.9rem;">Visualiza cuántas herramientas ha solicitado cada trabajador.</h3>
            </div>
        </a>

        <!-- Reporte de Herramientas -->
        <a href="{{ route('reportes.herramientas') }}" class="stat-card" style="text-decoration: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 3rem 1.5rem;">
            <div class="stat-icon" style="background: rgba(56, 189, 248, 0.1); color: #38BDF8; width: 80px; height: 80px; border-radius: 50%; font-size: 2.5rem; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <div class="stat-details">
                <p style="font-size: 1.25rem; margin-bottom: 0.5rem; color: #38BDF8;">Por Herramientas</p>
                <h3 style="color: var(--text-muted); font-weight: 400; text-transform: none; font-size: 0.9rem;">Analiza la demanda de herramientas y su historial de mantenimiento.</h3>
            </div>
        </a>

        <!-- Reporte Temporal -->
        <a href="{{ route('reportes.temporal') }}" class="stat-card" style="text-decoration: none; cursor: pointer; display: flex; flex-direction: column; align-items: center; text-align: center; padding: 3rem 1.5rem;">
            <div class="stat-icon" style="background: rgba(16, 185, 129, 0.1); color: #10B981; width: 80px; height: 80px; border-radius: 50%; font-size: 2.5rem; margin-bottom: 1.5rem;">
                <i class="fa-solid fa-calendar-days"></i>
            </div>
            <div class="stat-details">
                <p style="font-size: 1.25rem; margin-bottom: 0.5rem; color: #10B981;">Temporal</p>
                <h3 style="color: var(--text-muted); font-weight: 400; text-transform: none; font-size: 0.9rem;">Genera reportes por periodos de tiempo (mensual, semanal, personalizado).</h3>
            </div>
        </a>
    </div>
</div>
@endsection
