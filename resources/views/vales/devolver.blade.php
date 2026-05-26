@extends('layouts.app')

@section('title', 'Devolución Rápida (Por Vale)')

@section('content')
<div class="d-flex mb-4">
    <h1 class="page-title" style="margin-bottom:0;">Devolución Rápida (Por Vale)</h1>
    <a href="{{ route('vales.index') }}" class="btn" style="background:#172A45; color:var(--text-main); width:auto;">
        <i class="fa-solid fa-arrow-left mr-1"></i> Volver a Vales
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<div class="card" style="background: var(--surface-color); padding: 3rem 2rem; border-radius: 12px; border: 1px solid var(--border-color); max-width: 500px; margin: 0 auto; text-align: center;">
    <i class="fa-solid fa-barcode" style="font-size: 4rem; color: var(--primary-color); margin-bottom: 1.5rem;"></i>
    <h2 style="margin-bottom: 0.5rem;">Escanear Vale</h2>
    <p style="color: var(--text-muted); margin-bottom: 2rem;">Acerque la pistola lectora al código de barras del vale o ingrese el código manualmente.</p>

    <form action="{{ route('vales.buscar') }}" method="POST">
        @csrf
        <div class="form-group">
            <input type="text" name="codigo_vale" id="codigo_vale" class="form-control" placeholder="V-XXXX" style="font-size: 1.5rem; text-align: center; padding: 1rem; text-transform: uppercase;" required autofocus autocomplete="off">
        </div>
        <button type="submit" class="btn btn-primary" style="font-size: 1.1rem; width: 100%; padding: 1rem;">
            <i class="fa-solid fa-magnifying-glass mr-1"></i> Buscar Vale
        </button>
    </form>
</div>

<script>
    // Mantener el foco en el input para que la pistola láser escriba directamente
    window.onload = function() {
        document.getElementById('codigo_vale').focus();
    };
    // Auto-enviar formulario si se detecta un "Enter" que suelen mandar las pistolas láser
    document.getElementById('codigo_vale').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            e.preventDefault(); // Evita doble submit si la pistola manda el enter y luego el boton hace el suyo
            this.form.submit();
        }
    });
</script>
@endsection
