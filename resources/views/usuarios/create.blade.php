@extends('layouts.app')

@section('title', 'Crear Usuario')

@section('content')
<div class="d-flex mb-4">
    <h1 class="page-title" style="margin-bottom:0;">
        <i class="fa-solid fa-user-plus" style="color: var(--primary-color);"></i> Crear Nuevo Usuario
    </h1>
    <a href="{{ route('usuarios.index') }}" class="btn" style="background:#172A45; color:var(--text-main); width:auto;">
        <i class="fa-solid fa-arrow-left mr-1"></i> Volver a usuarios
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger">
        <ul style="margin:0; padding-left: 1.5rem;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger"><i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}</div>
@endif

<div class="card" style="background: var(--surface-color); padding: 2rem; border-radius: 12px; border: 1px solid var(--border-color); max-width: 560px;">
    <form action="{{ route('usuarios.store') }}" method="POST" autocomplete="off">
        @csrf

        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ej: Juan Pérez García" value="{{ old('nombre') }}" required autofocus maxlength="100">
        </div>

        <div class="form-group">
            <label for="usuario">Nombre de Usuario</label>
            <input type="text" id="usuario" name="usuario" class="form-control" placeholder="Ej: almacen2" value="{{ old('usuario') }}" required maxlength="50" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Contraseña</label>
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Mínimo 8 caracteres" required minlength="8" autocomplete="new-password">
            <small style="color: var(--text-muted); font-size: 0.8rem; margin-top: 0.35rem; display: block;">
                Usa combinación de letras, números y símbolos.
            </small>
        </div>

        <div class="form-group">
            <label for="rol">Rol del Sistema</label>
            <select id="rol" name="rol" class="form-control" required>
                <option value="Almacenero" {{ old('rol') == 'Almacenero' ? 'selected' : '' }}>🟠 Almacenero — Operativo (registra préstamos, herramientas, incidencias)</option>
                <option value="Supervisor" {{ old('rol') == 'Supervisor' ? 'selected' : '' }}>🟡 Supervisor — Consulta (solo lectura y reportes)</option>
                <option value="Administrador" {{ old('rol') == 'Administrador' ? 'selected' : '' }}>🔴 Administrador — Control total (incluye usuarios)</option>
            </select>
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fa-solid fa-user-plus mr-1"></i> Crear Usuario
            </button>
        </div>
    </form>
</div>
@endsection
