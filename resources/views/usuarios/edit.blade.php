@extends('layouts.app')

@section('title', 'Editar Usuario')

@section('content')
<div class="d-flex mb-4">
    <h1 class="page-title" style="margin-bottom:0;">
        <i class="fa-solid fa-user-pen" style="color: var(--primary-color);"></i> Editar Usuario
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
    <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" autocomplete="off">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="nombre">Nombre Completo</label>
            <input type="text" id="nombre" name="nombre" class="form-control"
                   value="{{ old('nombre', $usuario->nombre) }}" required autofocus maxlength="100">
        </div>

        <div class="form-group">
            <label for="usuario">Nombre de Usuario</label>
            <input type="text" id="usuario" name="usuario" class="form-control"
                   value="{{ old('usuario', $usuario->usuario) }}" required maxlength="50" autocomplete="off">
        </div>

        <div class="form-group">
            <label for="password">Nueva Contraseña <small style="color: var(--text-muted);">(dejar vacío para no cambiar)</small></label>
            <input type="password" id="password" name="password" class="form-control"
                   placeholder="Nueva contraseña (mínimo 8 caracteres)" minlength="8" autocomplete="new-password">
        </div>

        @php $esMiCuenta = ($usuario->id === Auth::id()); @endphp
        <div class="form-group">
            <label for="rol">Rol del Sistema</label>
            <select id="rol" name="rol" class="form-control" required {{ $esMiCuenta ? 'disabled' : '' }}>
                <option value="Almacenero" {{ old('rol', $usuario->rol) == 'Almacenero' ? 'selected' : '' }}>🟠 Almacenero</option>
                <option value="Supervisor" {{ old('rol', $usuario->rol) == 'Supervisor' ? 'selected' : '' }}>🟡 Supervisor</option>
                <option value="Administrador" {{ old('rol', $usuario->rol) == 'Administrador' ? 'selected' : '' }}>🔴 Administrador</option>
            </select>
            @if($esMiCuenta)
                <input type="hidden" name="rol" value="{{ $usuario->rol }}">
                <small style="color: #F59E0B; font-size: 0.8rem;">
                    <i class="fa-solid fa-lock"></i> No puedes cambiar tu propio rol.
                </small>
            @endif
        </div>

        <div style="margin-top: 2rem; display: flex; gap: 1rem;">
            <button type="submit" class="btn btn-primary" style="flex: 1;">
                <i class="fa-solid fa-save mr-1"></i> Guardar Cambios
            </button>
        </div>
    </form>
</div>
@endsection
