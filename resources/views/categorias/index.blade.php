@extends('layouts.app')

@section('title', 'Categorías: ' . $almacen->nombre)

@section('content')
<div class="logistics-header mb-5">
    <div class="header-main">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb bg-transparent p-0 mb-2" style="font-size: 0.85rem;">
                <li class="breadcrumb-item"><a href="{{ route('almacenes.index') }}" style="color: var(--primary-color);">Red de Nodos</a></li>
                <li class="breadcrumb-item active text-muted" aria-current="page">{{ $almacen->nombre }}</li>
            </ol>
        </nav>
        <h1 class="premium-title"><i class="fa-solid fa-tags mr-3"></i>Estructura Funcional</h1>
        <p class="premium-subtitle">Definición de categorías operativas para este nodo específico.</p>
    </div>
    @if(in_array(Auth::user()->rol, ['Administrador', 'Almacenero']))
    <button class="btn-premium" data-toggle="modal" data-target="#modalCrear">
        <i class="fa-solid fa-folder-plus mr-2"></i>Nueva Categoría
    </button>
    @endif
</div>

<div class="premium-card">
    <div class="table-responsive">
        <table class="premium-table">
            <thead>
                <tr>
                    <th>Función / Tipo</th>
                    <th>Especificaciones</th>
                    <th class="text-center">Activos en Stock</th>
                    <th class="text-right">Operaciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $cat)
                <tr>
                    <td>
                        <div class="cat-identity">
                            <div class="cat-indicator"></div>
                            <span class="cat-name">{{ $cat->nombre }}</span>
                        </div>
                    </td>
                    <td class="text-muted small">{{ $cat->descripcion ?: 'Sin descripción detallada' }}</td>
                    <td class="text-center">
                        <span class="stock-badge">{{ $cat->herramientas_count }}</span>
                    </td>
                    <td class="text-right">
                        <div class="action-btns">
                            @if(in_array(Auth::user()->rol, ['Administrador', 'Almacenero']))
                            <button class="btn-icon warn" data-toggle="modal" data-target="#modalEdit{{ $cat->id }}">
                                <i class="fa-solid fa-pen-nib"></i>
                            </button>
                            @endif
                            @if(Auth::user()->rol === 'Administrador')
                            <form action="{{ route('almacenes.categorias.destroy', [$almacen->id, $cat->id]) }}" method="POST" class="d-inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon danger" onclick="return confirm('¿Eliminar categoría?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-5">
                        <i class="fa-solid fa-folder-open fa-3x mb-3 text-muted" style="opacity: 0.3;"></i>
                        <p class="text-muted">No se han definido categorías funcionales para este nodo.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- MODALS -->
@foreach($categorias as $cat)
    @if(in_array(Auth::user()->rol, ['Administrador', 'Almacenero']))
    <div class="modal fade" id="modalEdit{{ $cat->id }}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <form action="{{ route('almacenes.categorias.update', [$almacen->id, $cat->id]) }}" method="POST" class="modal-content premium-modal">
                @csrf @method('PUT')
                <div class="modal-header">
                    <h5 class="modal-title text-primary">Editar Estructura</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="form-group-premium">
                        <label>Nombre de la Categoría</label>
                        <input type="text" name="nombre" class="form-input" value="{{ $cat->nombre }}" required>
                    </div>
                    <div class="form-group-premium">
                        <label>Descripción Operativa</label>
                        <textarea name="descripcion" class="form-input" rows="3">{{ $cat->descripcion }}</textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn-save">Guardar Cambios</button>
                </div>
            </form>
        </div>
    </div>
    @endif
@endforeach

@if(in_array(Auth::user()->rol, ['Administrador', 'Almacenero']))
<div class="modal fade" id="modalCrear" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form action="{{ route('almacenes.categorias.store', $almacen->id) }}" method="POST" class="modal-content premium-modal">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title text-primary">Añadir Categoría Operativa</h5>
                <button type="button" class="close text-white" data-dismiss="modal">&times;</button>
            </div>
            <div class="modal-body">
                <div class="form-group-premium">
                    <label>Nombre de Función</label>
                    <input type="text" name="nombre" class="form-input" placeholder="Ej. Neumáticas, Hidráulicas..." required>
                </div>
                <div class="form-group-premium">
                    <label>Alcance / Uso</label>
                    <textarea name="descripcion" class="form-input" rows="3" placeholder="Descripción breve del grupo..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-dismiss="modal">Cancelar</button>
                <button type="submit" class="btn-save">Registrar Categoría</button>
            </div>
        </form>
    </div>
</div>
@endif

<style>
/* CUSTOM CATEGORIES STYLES */
.logistics-header { display: flex; justify-content: space-between; align-items: flex-end; border-bottom: 1px solid var(--border-color); padding-bottom: 2rem; }
.premium-title { color: var(--primary-color); font-weight: 800; font-size: 2.2rem; margin: 0; }
.premium-subtitle { color: var(--text-muted); margin-top: 0.5rem; font-size: 1.1rem; }

.btn-premium { background: var(--primary-color); color: var(--bg-color); border: none; padding: 0.8rem 1.8rem; border-radius: 12px; font-weight: 800; transition: all 0.3s; cursor: pointer; }
.btn-premium:hover { transform: translateY(-3px); box-shadow: 0 8px 20px rgba(100, 255, 218, 0.3); }

.premium-card { background: var(--surface-color); border: 1px solid var(--border-color); border-radius: 20px; overflow: hidden; box-shadow: 0 15px 35px rgba(0,0,0,0.3); }
.premium-table { width: 100%; border-collapse: collapse; }
.premium-table th { background: rgba(10, 25, 47, 0.8); color: var(--text-muted); text-transform: uppercase; font-size: 0.75rem; letter-spacing: 1px; padding: 1.2rem; text-align: left; border-bottom: 1px solid var(--border-color); }
.premium-table td { padding: 1.2rem; border-bottom: 1px solid var(--border-color); color: #ccd6f6; vertical-align: middle; }
.premium-table tr:hover { background: rgba(100, 255, 218, 0.02); }

.cat-identity { display: flex; align-items: center; gap: 1rem; }
.cat-indicator { width: 4px; height: 24px; background: var(--primary-color); border-radius: 4px; box-shadow: 0 0 10px var(--primary-color); }
.cat-name { font-weight: 700; font-size: 1.1rem; color: var(--primary-color); }

.stock-badge { background: rgba(100, 255, 218, 0.1); color: var(--primary-color); font-weight: 800; padding: 5px 15px; border-radius: 50px; border: 1px solid rgba(100, 255, 218, 0.2); }

.action-btns { display: flex; gap: 0.8rem; justify-content: flex-end; }
.btn-icon { background: rgba(255,255,255,0.05); border: none; width: 36px; height: 36px; border-radius: 8px; color: var(--text-muted); transition: all 0.2s; cursor: pointer; }
.btn-icon:hover { color: white; background: rgba(255,255,255,0.1); }
.btn-icon.warn:hover { color: var(--warning); background: rgba(245, 158, 11, 0.1); }
.btn-icon.danger:hover { color: var(--danger); background: rgba(244, 63, 94, 0.1); }

/* MODAL STYLES */
.premium-modal { background: #0a192f; border: 1px solid var(--border-color); border-radius: 20px; box-shadow: 0 25px 50px rgba(0,0,0,0.5); }
.modal-header { border-bottom: 1px solid var(--border-color); padding: 1.5rem; }
.form-group-premium { margin-bottom: 1.5rem; }
.form-group-premium label { display: block; color: var(--text-muted); font-size: 0.85rem; font-weight: 700; margin-bottom: 0.5rem; text-transform: uppercase; letter-spacing: 1px; }
.form-input { width: 100%; background: #112240; border: 1px solid var(--border-color); border-radius: 10px; padding: 0.8rem 1.2rem; color: white; transition: all 0.3s; }
.form-input:focus { border-color: var(--primary-color); outline: none; box-shadow: 0 0 10px rgba(100, 255, 218, 0.1); }
.btn-cancel { background: transparent; border: 1px solid var(--border-color); color: var(--text-muted); padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 700; margin-right: 1rem; cursor: pointer; }
.btn-save { background: var(--primary-color); border: none; color: var(--bg-color); padding: 0.8rem 1.5rem; border-radius: 10px; font-weight: 800; cursor: pointer; }
</style>
@endsection
