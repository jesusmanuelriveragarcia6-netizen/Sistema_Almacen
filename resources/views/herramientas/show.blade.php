@extends('layouts.app')

@section('title', 'Expediente de Herramienta')

@section('content')
<style>
    .dossier-card {
        background: var(--surface-color);
        border: 1px solid rgba(255, 255, 255, 0.05);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.4);
    }

    .dossier-header {
        background: linear-gradient(to right, #112240, #0a192f);
        padding: 2.5rem;
        border-bottom: 1px solid var(--primary-color);
    }

    .info-label {
        color: #8892b0;
        text-transform: uppercase;
        font-size: 0.7rem;
        font-weight: 700;
        letter-spacing: 1.2px;
        margin-bottom: 0.25rem;
    }

    .info-value {
        color: #ccd6f6;
        font-size: 1.1rem;
        font-weight: 600;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 1.5rem;
        padding: 2rem;
        background: rgba(10, 25, 47, 0.5);
    }

    .stat-item {
        padding: 1rem;
        border-radius: 12px;
        background: #112240;
        border: 1px solid #233554;
        text-align: center;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 800;
        color: #64ffda;
    }

    .history-section {
        margin-top: 3rem;
        padding: 2rem;
    }

    .barcode-container {
        background: #fff;
        padding: 1.5rem;
        border-radius: 12px;
        display: inline-block;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }

    .action-bar {
        padding: 1.5rem 2.5rem;
        background: rgba(0,0,0,0.2);
        display: flex;
        gap: 1rem;
    }
</style>

<div class="container-fluid pb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0" style="background: transparent; padding: 0;">
                <li class="breadcrumb-item"><a href="{{ route('herramientas.index') }}" style="color: var(--primary-color);">Catálogo</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">{{ $herramienta->codigo }}</li>
            </ol>
        </nav>
        <a href="{{ route('herramientas.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="fa-solid fa-arrow-left mr-2"></i>Regresar
        </a>
    </div>

    <div class="dossier-card">
        <div class="dossier-header d-flex justify-content-between align-items-start flex-wrap gap-4">
            <div style="flex: 1; min-width: 280px;">
                <div class="badge-modern badge {{ $herramienta->estado }} mb-3">{{ $herramienta->estado }}</div>
                <h1 style="color: #64ffda; font-weight: 800; font-size: 2.2rem; margin-bottom: 0.5rem;">{{ $herramienta->nombre }}</h1>
                <p style="color: #8892b0; font-size: 1rem; max-width: 500px;">{{ $herramienta->descripcion ?: 'Sin descripción detallada.' }}</p>
            </div>

            {{-- Imagen de la Herramienta --}}
            @php
                $hasPng = file_exists(public_path('img/herramientas/' . $herramienta->id . '.png'));
                $hasSvg = file_exists(public_path('img/herramientas/' . $herramienta->id . '.svg'));
            @endphp
            <div style="
                width: 160px; height: 160px;
                border-radius: 16px;
                background: linear-gradient(135deg, #112240, #0a192f);
                border: 2px solid rgba(100,255,218,0.3);
                display: flex; align-items: center; justify-content: center;
                overflow: hidden;
                box-shadow: 0 0 30px rgba(100,255,218,0.1);
                flex-shrink: 0;
            ">
                @if($hasPng)
                    <img src="/img/herramientas/{{ $herramienta->id }}.png"
                         alt="{{ $herramienta->nombre }}"
                         style="width:100%;height:100%;object-fit:cover;">
                @elseif($hasSvg)
                    <img src="/img/herramientas/{{ $herramienta->id }}.svg"
                         alt="{{ $herramienta->nombre }}"
                         style="width:100%;height:100%;object-fit:contain;padding:8px;">
                @else
                    <i class="fa-solid fa-screwdriver-wrench" style="font-size:4rem;color:rgba(100,255,218,0.25);"></i>
                @endif
            </div>

            <div class="text-center">
                <div class="barcode-container mb-2">
                    <svg id="barcode"></svg>
                </div>
                <div style="color: #ccd6f6; letter-spacing: 4px; font-weight: 700; font-size: 0.9rem;">{{ $herramienta->codigo }}</div>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-item">
                <div class="info-label">Stock Disponible</div>
                <div class="stat-value" style="color: {{ $herramienta->stock_disponible > 0 ? '#64ffda' : '#ff4d4d' }};">
                    {{ $herramienta->stock_disponible }}
                </div>
                <small style="color: #8892b0;">de {{ $herramienta->stock_total }} unidades</small>
            </div>
            <div class="stat-item">
                <div class="info-label">Ubicación Actual</div>
                <div class="info-value" style="color: #ccd6f6;">{{ $herramienta->ubicacion }}</div>
                <small style="color: #8892b0;">{{ $herramienta->modelAlmacen ? $herramienta->modelAlmacen->nombre : 'Almacén Principal' }}</small>
            </div>
            <div class="stat-item">
                <div class="info-label">Sección / Área</div>
                <div class="info-value text-primary">{{ $herramienta->categoria ? $herramienta->categoria->nombre : 'General' }}</div>
                <small style="color: #8892b0;">Control de Flujo</small>
            </div>
            <div class="stat-item">
                <div class="info-label">Propósito / Uso</div>
                <div class="info-value">{{ $herramienta->uso ?: 'Multipropósito' }}</div>
                <small style="color: #8892b0;">{{ $herramienta->tamano ?: 'Estándar' }}</small>
            </div>
        </div>

        <div class="action-bar border-top border-dark">
            @if(in_array(Auth::user()->rol, ['Administrador', 'Almacenero']))
                <a href="{{ route('herramientas.edit', $herramienta->id) }}" class="btn btn-warning px-4 font-weight-bold">
                    <i class="fa-solid fa-pen-to-square mr-2"></i>Modificar Registro
                </a>
            @endif
            <button onclick="window.print()" class="btn btn-outline-primary px-4">
                <i class="fa-solid fa-print mr-2"></i>Imprimir Etiqueta
            </button>
            @if(Auth::user()->rol === 'Administrador')
                <form action="{{ route('herramientas.destroy', $herramienta->id) }}" method="POST" class="ml-auto" onsubmit="return confirm('¿Eliminar permanentemente?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-link text-danger" style="text-decoration: none;">
                        <i class="fa-solid fa-trash mr-2"></i>Eliminar del Sistema
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Historial Maestro -->
    <div class="history-section">
        <h3 style="color: #ccd6f6; font-weight: 700; margin-bottom: 2rem;">
            <i class="fa-solid fa-timeline mr-2 text-primary"></i>Cronología de Operaciones
        </h3>
        
        <div class="table-responsive">
            <table class="table table-dark table-hover" style="background: transparent;">
                <thead style="border-bottom: 2px solid #233554;">
                    <tr>
                        <th class="text-muted border-0">Timestamp</th>
                        <th class="text-muted border-0">Folio Vale</th>
                        <th class="text-muted border-0">Responsable Operativo</th>
                        <th class="text-center text-muted border-0">Carga</th>
                        <th class="text-center text-muted border-0">Retorno</th>
                        <th class="text-right text-muted border-0">Status Operativo</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($historial as $h_item)
                    <tr style="border-bottom: 1px solid #112240;">
                        <td><small style="color: #8892b0;">{{ \Carbon\Carbon::parse($h_item->vale->fecha_creacion)->format('d M, Y | H:i') }}</small></td>
                        <td>
                            <a href="{{ url('vales/' . $h_item->vale->id) }}" style="color: #64ffda; font-family: 'Courier New', Courier, monospace; font-weight: 700;">
                                {{ $h_item->vale->codigo_vale }}
                            </a>
                        </td>
                        <td>
                            <div style="font-weight: 600; color: #ccd6f6;">{{ $h_item->vale->trabajador->nombre }} {{ $h_item->vale->trabajador->apellidos }}</div>
                            <small style="color: #8892b0;">DNI: {{ $h_item->vale->trabajador->dni }}</small>
                        </td>
                        <td class="text-center font-weight-bold" style="color: #ccd6f6;">{{ $h_item->cantidad_prestada }}</td>
                        <td class="text-center">
                            <span style="font-weight: 800; color: {{ $h_item->cantidad_devuelta >= $h_item->cantidad_prestada ? '#64ffda' : '#ff4d4d' }};">
                                {{ $h_item->cantidad_devuelta }}
                            </span>
                        </td>
                        <td class="text-right">
                            <span class="badge-modern badge {{ $h_item->vale->estado }}">{{ $h_item->vale->estado }}</span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted italic">Sin operaciones registradas en el periodo actual.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    JsBarcode("#barcode", "{{ $herramienta->codigo }}", {
        format: "CODE128",
        lineColor: "#000",
        width: 1.5,
        height: 40,
        displayValue: false
    });
</script>

<style>
@media print {
    body * { visibility: hidden; }
    .dossier-card, .dossier-card * { visibility: visible; }
    .dossier-card { position: absolute; left: 0; top: 0; width: 100%; border: none; box-shadow: none; }
    .action-bar, .breadcrumb, .btn { display: none !important; }
}
</style>
@endsection
