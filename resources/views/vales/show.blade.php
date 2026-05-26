@extends('layouts.app')

@section('title', 'Comprobante de Vale de Salida')

@section('content')
<div class="d-flex mb-4 hide-print">
    <h1 class="page-title" style="margin-bottom:0;">Comprobante de Vale de Salida</h1>
    <a href="{{ route('vales.index') }}" class="btn" style="background:#172A45; color:var(--text-main); width:auto;">
        <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Vales
    </a>
</div>

@if (session('exito') || request()->has('exito'))
    <div class="alert hide-print" style="background: rgba(16,185,129,0.1); color: #10B981; border: 1px solid #10B981;">
        <i class="fa-solid fa-check-circle"></i> Vale de Salida generado correctamente.
    </div>
@endif
@if (session('devolucion') || request()->has('devolucion'))
    <div class="alert hide-print" style="background: rgba(56,189,248,0.1); color: #38BDF8; border: 1px solid #38BDF8;">
        <i class="fa-solid fa-check-circle"></i> Devolución procesada correctamente.
    </div>
@endif

<div class="vale-print-container" style="background: #fff; color: #000; padding: 2rem; border-radius: 8px; max-width: 800px; margin: 0 auto;">
    
    <!-- Encabezado del Vale -->
    <div style="display: flex; justify-content: space-between; align-items: center; border-bottom: 2px solid #ccc; padding-bottom: 1rem; margin-bottom: 2rem;">
        <div>
            <h2 style="margin:0; color:#333; font-size: 1.8rem; font-weight: 800;">ALMACÉN INTELIGENTE</h2>
            <p style="margin: 0.25rem 0 0 0; color:#666; font-size: 0.9rem;">Documento Oficial de Salida</p>
        </div>
        <div style="text-align: right;">
            <p style="margin: 0; font-weight: bold; font-size: 1.2rem;">VALE DE SALIDA</p>
            <p style="margin: 0; color:#666;">{{ \Carbon\Carbon::parse($vale->fecha_creacion)->format('d/m/Y H:i') }}</p>
        </div>
    </div>

    <!-- Código de Barras del Vale -->
    <div style="text-align: center; margin-bottom: 2rem;">
        <img src="https://barcodeapi.org/api/128/{{ urlencode($vale->codigo_vale) }}" alt="Barcode" style="height: 60px;">
        <p style="margin: 0.5rem 0 0 0; font-weight: bold; font-size: 1.1rem; letter-spacing: 2px;">{{ $vale->codigo_vale }}</p>
    </div>

    <!-- Datos -->
    <div style="display: flex; gap: 2rem; margin-bottom: 2rem;">
        <div style="flex: 1; background: #f9f9f9; padding: 1rem; border-radius: 4px; border: 1px solid #eee;">
            <h4 style="margin: 0 0 0.5rem 0; color:#444; border-bottom: 1px solid #ccc; padding-bottom:0.25rem;">Datos del Solicitante</h4>
            <p style="margin: 0.25rem 0;"><strong>Nombre:</strong> {{ $vale->trabajador->nombre }} {{ $vale->trabajador->apellidos }}</p>
            <p style="margin: 0.25rem 0;"><strong>DNI:</strong> {{ $vale->trabajador->dni }}</p>
        </div>
        <div style="flex: 1; background: #f9f9f9; padding: 1rem; border-radius: 4px; border: 1px solid #eee;">
            <h4 style="margin: 0 0 0.5rem 0; color:#444; border-bottom: 1px solid #ccc; padding-bottom:0.25rem;">Datos de Emisión</h4>
            <p style="margin: 0.25rem 0;"><strong>Emitido por:</strong> {{ $vale->usuario->nombre }}</p>
            <p style="margin: 0.25rem 0;"><strong>Estado del Vale:</strong> {{ $vale->estado }}</p>
        </div>
    </div>

    <!-- Detalles -->
    <h4 style="margin: 0 0 1rem 0; color:#333;">Herramientas Solicitadas</h4>
    <table style="width: 100%; border-collapse: collapse; margin-bottom: 2rem;">
        <thead>
            <tr style="background-color: #f0f0f0;">
                <th style="padding: 0.75rem; border: 1px solid #ccc; text-align: left;">Código</th>
                <th style="padding: 0.75rem; border: 1px solid #ccc; text-align: left;">Herramienta</th>
                <th style="padding: 0.75rem; border: 1px solid #ccc; text-align: center;">Cant. Prestada</th>
                <th style="padding: 0.75rem; border: 1px solid #ccc; text-align: center;">Cant. Devuelta</th>
            </tr>
        </thead>
        <tbody>
            @foreach($vale->detalles as $d)
            <tr>
                <td style="padding: 0.75rem; border: 1px solid #ccc; font-weight: bold;">{{ $d->herramienta->codigo }}</td>
                <td style="padding: 0.75rem; border: 1px solid #ccc;">{{ $d->herramienta->nombre }}</td>
                <td style="padding: 0.75rem; border: 1px solid #ccc; text-align: center;">{{ $d->cantidad_prestada }}</td>
                <td style="padding: 0.75rem; border: 1px solid #ccc; text-align: center; color: {{ $d->cantidad_devuelta === $d->cantidad_prestada ? 'green' : 'red' }};">
                    {{ $d->cantidad_devuelta }}
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Firmas -->
    <div style="display: flex; justify-content: space-around; margin-top: 4rem;">
        <div style="text-align: center; width: 200px;">
            <div style="border-bottom: 1px solid #000; height: 40px; margin-bottom: 0.5rem;"></div>
            <p style="margin:0; font-size: 0.9rem; color:#555;">Firma Almacenero</p>
        </div>
        <div style="text-align: center; width: 200px;">
            <div style="border-bottom: 1px solid #000; height: 40px; margin-bottom: 0.5rem;"></div>
            <p style="margin:0; font-size: 0.9rem; color:#555;">Firma Solicitante</p>
        </div>
    </div>
</div>

<div class="hide-print" style="text-align: center; margin-top: 2rem;">
    <button onclick="window.print()" class="btn btn-primary" style="font-size: 1.1rem; padding: 0.75rem 2rem;">
        <i class="fa-solid fa-print mr-1"></i> Imprimir Vale
    </button>
</div>

<style>
@media print {
    body { background: #fff !important; margin:0; padding:0; }
    .sidebar, .main-header, .hide-print { display: none !important; }
    .main-content { overflow: visible !important; }
    .wrapper { height: auto !important; overflow: visible !important; }
    .vale-print-container { box-shadow: none !important; max-width: 100% !important; border: none !important; }
}
</style>
@endsection
