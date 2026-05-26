@extends('layouts.app')

@section('title', 'Procesar Devolución')

@section('content')
<div class="d-flex mb-4">
    <h1 class="page-title" style="margin-bottom:0;">Procesar Devolución</h1>
    <a href="{{ route('vales.devolver_form') }}" class="btn" style="background:#172A45; color:var(--text-main); width:auto;">
        <i class="fa-solid fa-arrow-left mr-1"></i> Escanear Otro Vale
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<div style="display: flex; gap: 2rem; flex-wrap: wrap;">
    <!-- Resumen del Vale -->
    <div class="card" style="flex: 1; min-width: 300px; background: var(--surface-color); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color); height: fit-content; text-align: center;">
        <h3 style="color:var(--primary-color); margin-bottom: 0.5rem; font-size: 1.8rem;"><i class="fa-solid fa-file-invoice mr-1"></i></h3>
        <h2 style="margin-bottom: 1rem; color: var(--text-main); font-weight: bold; font-size: 1.5rem; letter-spacing: 1px;">{{ $vale->codigo_vale }}</h2>
        <p style="margin-bottom: 0;"><span class="badge {{ $vale->estado }}">{{ $vale->estado }}</span></p>
    </div>

    <!-- Formulario de Devolución -->
    <div class="card" style="flex: 2; min-width: 400px; background: var(--surface-color); padding: 1.5rem; border-radius: 12px; border: 1px solid var(--border-color);">
        <h3 style="margin-bottom: 1rem;">Herramientas en este Vale</h3>
        <form action="{{ route('vales.guardar_devolucion', $vale->id) }}" method="POST">
            @csrf
            
            <div class="table-container" style="margin-bottom: 1.5rem;">
                <table class="table" style="font-size: 0.95rem;">
                    <thead>
                        <tr>
                            <th>Código</th>
                            <th>Herramienta</th>
                            <th>Prestado</th>
                            <th>Devuelto</th>
                            <th>A Devolver Ahora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $todo_devuelto = true; @endphp
                        @foreach($vale->detalles as $d)
                        @php
                            $prestado = $d->cantidad_prestada;
                            $devuelto = $d->cantidad_devuelta;
                            $pendiente = $prestado - $devuelto;
                            if($pendiente > 0) $todo_devuelto = false;
                        @endphp
                        <tr>
                            <td style="font-weight: bold; color: var(--primary-color);">{{ $d->herramienta->codigo }}</td>
                            <td>{{ $d->herramienta->nombre }}</td>
                            <td style="text-align: center; font-weight: bold;">{{ $prestado }}</td>
                            <td style="text-align: center; color: {{ $devuelto === $prestado ? 'var(--primary-color)' : 'var(--warning)' }};">{{ $devuelto }}</td>
                            <td>
                                @if($pendiente > 0)
                                    <div style="display: flex; gap: 0.5rem; align-items: center;">
                                        <input type="number" name="devolver_cant[{{ $d->id }}]" class="form-control" style="width: 70px; padding: 0.25rem; background: rgba(100, 255, 218, 0.1); border-color: var(--primary-color);" min="0" max="{{ $pendiente }}" value="{{ $pendiente }}">
                                        <a href="{{ route('incidencias.index', ['herramienta_id' => $d->herramienta_id, 'vale_id' => $vale->id, 'trabajador_id' => $vale->trabajador_id]) }}" class="btn btn-secondary" style="padding: 0.35rem 0.6rem; font-size: 0.8rem; background: rgba(239, 68, 68, 0.1); color: #EF4444; border: 1px solid #EF4444;" title="Reportar Pérdida/Daño">
                                            <i class="fa-solid fa-triangle-exclamation"></i>
                                        </a>
                                    </div>
                                @else
                                    <span style="color: var(--primary-color); font-size: 0.85rem;"><i class="fa-solid fa-check"></i> Completo</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            @if(!$todo_devuelto)
            <button type="submit" class="btn btn-primary" style="width: 100%; font-size: 1.1rem; padding: 1rem;">
                <i class="fa-solid fa-check-double mr-1"></i> Registrar Devolución
            </button>
            @else
            <div class="alert" style="background: rgba(16,185,129,0.1); color: #10B981; border: 1px solid #10B981; text-align: center;">
                <i class="fa-solid fa-check-circle"></i> Todas las herramientas de este vale han sido devueltas.
            </div>
            @endif
        </form>
    </div>
</div>
@endsection
