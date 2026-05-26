@extends('layouts.app')

@section('title', 'Ex-Trabajadores')

@section('content')
<div class="d-flex mb-4" style="flex-wrap:wrap; gap:1rem;">
    <div style="flex:1;">
        <h1 class="page-title" style="margin-bottom:0;">Ex-Trabajadores</h1>
        <p style="color:var(--text-muted); margin-top:0.25rem; font-size:0.9rem;">
            Archivo histórico de personal dado de baja. Su historial de vales se conserva intacto.
        </p>
    </div>
    <div style="display:flex; gap:1rem; align-items:flex-start;">
        <form action="{{ route('trabajadores.inactivos') }}" method="GET" style="display: flex; gap: 0.5rem; margin: 0;">
            <input type="text" name="search" id="filtro_inactivo" class="form-control" placeholder="Buscar por DNI o Nombre..." value="{{ $busqueda }}" style="width:260px; padding:0.5rem;">
            <button type="submit" class="btn btn-secondary" style="padding: 0.5rem 1rem;">Buscar</button>
            @if(!empty($busqueda))
                <a href="{{ route('trabajadores.inactivos') }}" class="btn btn-secondary" style="display: flex; align-items: center; justify-content: center;"><i class="fa-solid fa-xmark"></i></a>
            @endif
        </form>

        <a href="{{ route('trabajadores.index') }}" class="btn" style="background:var(--surface-color); border:1px solid var(--border-color); color:var(--text-main); white-space:nowrap;">
            <i class="fa-solid fa-users mr-1"></i> Personal Activo
        </a>
    </div>
</div>

<div class="table-container">
    <table class="table" id="tabla_inactivos">
        <thead>
            <tr>
                <th>DNI</th>
                <th>Nombre Completo</th>
                <th>Cargo</th>
                <th>Teléfono</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @if($trabajadores->isEmpty())
            <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">
                <i class="fa-solid fa-box-open" style="font-size:2rem; margin-bottom:0.5rem; display:block; opacity:0.4;"></i>
                No hay ex-trabajadores registrados.
            </td></tr>
            @else
                @foreach($trabajadores as $t)
                <tr class="inactivo-row">
                    <td class="col-dni" style="font-weight:500; color:var(--text-muted);">{{ $t->dni }}</td>
                    <td class="col-nombre" style="color:var(--text-muted);">
                        {{ $t->nombre }} {{ $t->apellidos }}
                    </td>
                    <td>{{ $t->cargo ?: '—' }}</td>
                    <td>{{ $t->telefono ?: '—' }}</td>
                    <td>
                        <span style="color:#EF4444; background:rgba(239,68,68,0.1); padding:4px 10px; border-radius:4px; font-size:0.85rem;">
                            <i class="fa-solid fa-user-slash"></i> Inactivo
                        </span>
                    </td>
                    <td>
                        <a href="{{ route('trabajadores.show', $t->id) }}" class="action-btn" title="Ver Expediente Completo" style="color:var(--primary-color);">
                            <i class="fa-solid fa-folder-open"></i> Ver Expediente
                        </a>
                        @if(Auth::user()->rol === 'Administrador')
                        <!-- Reactivar trabajador -->
                        <form action="{{ route('trabajadores.reactivar', $t->id) }}" method="POST" style="display:inline-block;" onsubmit="return confirm('¿Reactivar a este trabajador?');">
                            @csrf
                            <button type="submit" class="action-btn" title="Reactivar" style="border:none; cursor:pointer; color:#10B981;">
                                <i class="fa-solid fa-user-check"></i>
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @endforeach
            @endif
        </tbody>
    </table>
</div>

@if($trabajadores->hasPages())
<div style="display: flex; justify-content: center; gap: 0.5rem; margin-top: 2rem; margin-bottom: 2rem;">
    {{ $trabajadores->appends(['search' => $busqueda])->links('vendor.pagination.custom') }}
</div>
@endif

@endsection
