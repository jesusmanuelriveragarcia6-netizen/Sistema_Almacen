@extends('layouts.app')

@section('title', 'Editar Herramienta')

@section('content')
<div class="d-flex mb-4" style="align-items: center; justify-content: space-between;">
    <h1 class="page-title" style="margin-bottom:0;">Editar Herramienta</h1>
    <a href="{{ route('herramientas.index') }}" class="btn" style="background:#172A45; color:var(--text-main); width:auto;">
        <i class="fa-solid fa-arrow-left mr-1"></i> Volver al catálogo
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
    </div>
@endif

<div class="card" style="background: var(--surface-color); padding: 2rem; border-radius: 12px; border: 1px solid var(--border-color); max-width: 600px;">
    <form action="{{ route('herramientas.update', $herramienta->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-group">
            <label for="codigo">Código Único</label>
            <input type="text" id="codigo" name="codigo" class="form-control" value="{{ $herramienta->codigo }}" readonly style="background-color: transparent; color: var(--text-muted); cursor: not-allowed; border: 1px dashed var(--border-color);">
        </div>

        <div class="form-group">
            <label for="nombre">Nombre de Herramienta</label>
            <input type="text" id="nombre" name="nombre" class="form-control" value="{{ old('nombre', $herramienta->nombre) }}" required maxlength="150">
        </div>

        <div class="form-group">
            <label for="descripcion">Descripción / Marca / Modelo</label>
            <textarea id="descripcion" name="descripcion" class="form-control" rows="3">{{ old('descripcion', $herramienta->descripcion) }}</textarea>
        </div>

        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex:1;">
                <label for="almacen_id">Almacén de Ubicación</label>
                <select id="almacen_id" name="almacen_id" class="form-control" required onchange="filterCategories()">
                    @foreach($almacenes as $alm)
                        <option value="{{ $alm->id }}" {{ old('almacen_id', $herramienta->almacen_id) == $alm->id ? 'selected' : '' }}>{{ $alm->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-group" style="flex:1;">
                <label for="categoria_id">Categoría (Función)</label>
                <select id="categoria_id" name="categoria_id" class="form-control" required>
                    <!-- Will be populated by JS -->
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="ubicacion">Ubicación Exacta (Estante / Zona)</label>
            <input type="text" id="ubicacion" name="ubicacion" class="form-control" value="{{ old('ubicacion', $herramienta->ubicacion) }}" required maxlength="100">
        </div>

        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex:1;">
                <label for="tamano">Tamaño / Medida</label>
                <input type="text" id="tamano" name="tamano" class="form-control" value="{{ old('tamano', $herramienta->tamano) }}" maxlength="50">
            </div>
            <div class="form-group" style="flex:1;">
                <label for="uso">Uso / Propósito</label>
                <input type="text" id="uso" name="uso" class="form-control" value="{{ old('uso', $herramienta->uso) }}" maxlength="200">
            </div>
        </div>

        <div class="form-group">
            <label for="estado">Estado General</label>
            <select id="estado" name="estado" class="form-control" required>
                <option value="Disponible"    {{ old('estado', $herramienta->estado) === 'Disponible'    ? 'selected' : '' }}>Disponible</option>
                <option value="Mantenimiento" {{ old('estado', $herramienta->estado) === 'Mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                <option value="Perdido"       {{ old('estado', $herramienta->estado) === 'Perdido'       ? 'selected' : '' }}>Perdido</option>
                <option value="Dañado"        {{ old('estado', $herramienta->estado) === 'Dañado'        ? 'selected' : '' }}>Dañado</option>
                <option value="Falla técnica" {{ old('estado', $herramienta->estado) === 'Falla técnica' ? 'selected' : '' }}>Falla técnica</option>
            </select>
        </div>

        <div style="display: flex; gap: 1rem;">
            <div class="form-group" style="flex:1;">
                <label for="stock_total">Stock Total</label>
                <input type="number" id="stock_total" name="stock_total" class="form-control" value="{{ old('stock_total', $herramienta->stock_total) }}" min="1" required>
            </div>
            <div class="form-group" style="flex:1;">
                <label for="stock_minimo">Stock Crítico (Alerta)</label>
                <input type="number" id="stock_minimo" name="stock_minimo" class="form-control" value="{{ old('stock_minimo', $herramienta->stock_minimo) }}" min="0" required>
            </div>
        </div>

        <div style="margin-top: 2rem;">
            <button type="submit" class="btn btn-primary btn-lg btn-block"><i class="fa-solid fa-save mr-2"></i>Actualizar Registro</button>
        </div>
    </form>
</div>

<script>
    const data = @json($almacenes);
    const currentCategoria = "{{ old('categoria_id', $herramienta->categoria_id) }}";

    function filterCategories() {
        const almacenId = document.getElementById('almacen_id').value;
        const categoriaSelect = document.getElementById('categoria_id');
        
        categoriaSelect.innerHTML = '<option value="">Seleccione Categoría...</option>';
        
        if (almacenId) {
            const almacen = data.find(a => a.id == almacenId);
            if (almacen && almacen.categorias.length > 0) {
                almacen.categorias.forEach(cat => {
                    const option = document.createElement('option');
                    option.value = cat.id;
                    option.textContent = cat.nombre;
                    if (cat.id == currentCategoria) option.selected = true;
                    categoriaSelect.appendChild(option);
                });
                categoriaSelect.disabled = false;
            } else {
                categoriaSelect.innerHTML = '<option value="">Sin categorías en este almacén</option>';
                categoriaSelect.disabled = true;
            }
        }
    }

    window.onload = filterCategories;
</script>
@endsection
