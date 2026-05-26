@extends('layouts.app')

@section('title', 'Reporte de Incidencias')

@section('content')
<!-- Select2 CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

<style>
/* ── Select2 Dark Theme ── */
.select2-container--default .select2-selection--single {
    background: var(--surface-color);
    border: 1px solid var(--border-color);
    border-radius: 8px;
    height: 44px;
    display: flex;
    align-items: center;
    transition: border-color .2s;
}
.select2-container--default.select2-container--focus .select2-selection--single,
.select2-container--default.select2-container--open .select2-selection--single {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 3px rgba(13,148,136,.15);
}
.select2-container--default .select2-selection--single .select2-selection__rendered { color: var(--text-main); padding-left: 12px; line-height: normal; }
.select2-container--default .select2-selection__arrow { height: 44px; }
.select2-dropdown { background: #0d1b2a; border: 1px solid var(--border-color); border-radius: 8px; overflow: hidden; }
.select2-search--dropdown { padding: 8px; background: #0d1b2a; }
.select2-search--dropdown .select2-search__field {
    background: #081628; color: var(--text-main);
    border: 1px solid var(--border-color); border-radius: 6px; padding: 6px 10px;
}
.select2-results__option { color: var(--text-muted); padding: 8px 12px; }
.select2-container--default .select2-results__option--highlighted.select2-results__option--selectable {
    background: rgba(13,148,136,.25); color: var(--text-main);
}
.select2-container--default .select2-results__option--selected { background: rgba(13,148,136,.12); color: var(--primary-color); }

/* ── Tipo de incidencia cards ── */
.inc-tipo-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: .75rem; margin-bottom: 1.5rem; }
.inc-tipo-card {
    border: 2px solid var(--border-color); border-radius: 10px;
    padding: .85rem .5rem; text-align: center; cursor: pointer;
    transition: border-color .2s, background .2s, transform .15s;
    position: relative;
}
.inc-tipo-card:hover { transform: translateY(-2px); }
.inc-tipo-card.selected { border-color: var(--accent-color, #F59E0B); background: rgba(245,158,11,.07); }
.inc-tipo-card input[type=radio] { position: absolute; opacity: 0; width: 0; height: 0; }
.inc-tipo-card .tipo-icon { font-size: 1.6rem; margin-bottom: .35rem; display: block; }
.inc-tipo-card .tipo-label { font-size: .78rem; font-weight: 600; letter-spacing: .03em; }

/* Colores por tipo */
.inc-tipo-card[data-tipo="Dañado"]       { --accent-color: #EF4444; }
.inc-tipo-card[data-tipo="Mantenimiento"]{ --accent-color: #F59E0B; }
.inc-tipo-card[data-tipo="Perdido"]      { --accent-color: #8B5CF6; }
.inc-tipo-card[data-tipo="Revision"]     { --accent-color: #38BDF8; }
.inc-tipo-card[data-tipo="Falla técnica"]{ --accent-color: #EF4444; }
.inc-tipo-card[data-tipo="Dañado"].selected        .tipo-icon { color: #EF4444; }
.inc-tipo-card[data-tipo="Mantenimiento"].selected .tipo-icon { color: #F59E0B; }
.inc-tipo-card[data-tipo="Perdido"].selected       .tipo-icon { color: #8B5CF6; }
.inc-tipo-card[data-tipo="Revision"].selected      .tipo-icon { color: #38BDF8; }
.inc-tipo-card[data-tipo="Falla técnica"].selected .tipo-icon { color: #EF4444; }

/* Fade-in animation */
@keyframes fadeInUp {
    from { opacity:0; transform:translateY(18px); }
    to   { opacity:1; transform:translateY(0);    }
}
.fade-in { animation: fadeInUp .35s ease both; }

/* Resumen card */
.resumen-card {
    background: rgba(16,185,129,.07);
    border: 1px solid rgba(16,185,129,.35);
    border-radius: 12px; padding: 1.25rem; margin-bottom: 1.5rem;
    animation: fadeInUp .4s ease;
}
</style>

<!-- Page header -->
<div class="d-flex mb-4" style="flex-wrap:wrap; gap:1rem;">
    <div style="flex:1;">
        <h1 class="page-title" style="margin-bottom:0;"><i class="fa-solid fa-triangle-exclamation" style="color:#EF4444;"></i> Reporte de Incidencias</h1>
        <p style="color:var(--text-muted); margin-top:.25rem; font-size:.9rem;">Registra situaciones anómalas con las herramientas del almacén.</p>
    </div>
</div>

<!-- Flash Resumen (tras éxito) -->
@if(session('resumen'))
<div class="resumen-card">
    <div style="display:flex; gap:.75rem; align-items:center; margin-bottom:.75rem;">
        <i class="fa-solid fa-check-circle" style="color:#10B981; font-size:1.4rem;"></i>
        <strong style="color:#10B981; font-size:1.05rem;">Incidencia registrada exitosamente</strong>
    </div>
    <div style="display:grid; grid-template-columns:repeat(auto-fit,minmax(160px,1fr)); gap:.5rem; font-size:.9rem; color:var(--text-muted);">
        <div><span style="color:var(--text-main); font-weight:600;">Herramienta:</span><br>{{ session('resumen')['herramienta'] }}</div>
        <div><span style="color:var(--text-main); font-weight:600;">Tipo:</span><br>{{ session('resumen')['estado'] }}</div>
        <div><span style="color:var(--text-main); font-weight:600;">Unidades afectadas:</span><br>{{ session('resumen')['cantidad'] }}</div>
        <div><span style="color:var(--text-main); font-weight:600;">Registrado por:</span><br>{{ session('resumen')['usuario'] }}</div>
        <div><span style="color:var(--text-main); font-weight:600;">Fecha:</span><br>{{ session('resumen')['fecha'] }}</div>
    </div>
    @if(!empty(session('resumen')['descripcion']))
    <div style="margin-top:.75rem; font-size:.85rem; color:var(--text-muted);"><strong style="color:var(--text-main);">Notas:</strong> {{ session('resumen')['descripcion'] }}</div>
    @endif
</div>
@endif

@if ($errors->any())
<div class="alert alert-danger fade-in">
    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
</div>
@endif

<!-- Main layout -->
<div style="display:grid; grid-template-columns:1fr 1fr; gap:1.5rem; align-items:start;">

    <!-- ──── FORMULARIO ──── -->
    <div class="card fade-in" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:14px; padding:1.75rem;">
        <h3 style="margin:0 0 1.5rem; color:var(--text-main);">
            <i class="fa-solid fa-pen-to-square" style="color:#EF4444;"></i> Registrar Incidencia
        </h3>

        @if(Auth::user()->rol === 'Supervisor')
            <div style="background:rgba(239,68,68,.08); border:1px solid #EF4444; border-radius:8px; padding:1rem; text-align:center;">
                <i class="fa-solid fa-ban" style="color:#EF4444;"></i>
                <span style="color:#EF4444; margin-left:.5rem;">No tienes permisos para registrar incidencias.</span>
            </div>
        @else
        <form method="POST" action="{{ route('incidencias.store') }}" id="formIncidencia">
            @csrf
            
            <!-- Herramienta con Select2 -->
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label style="color:var(--text-muted); font-size:.85rem; font-weight:600; display:block; margin-bottom:.5rem; text-transform:uppercase; letter-spacing:.05em;">
                    Herramienta Afectada *
                </label>
                <select name="herramienta_id" id="sel_herramienta" class="form-control" required style="width:100%;">
                    <option value="">Buscar herramienta por código o nombre...</option>
                    @foreach($herramientas as $h)
                    <option value="{{ $h->id }}"
                        data-stock="{{ $h->stock_disponible }}"
                        data-estado="{{ $h->estado }}"
                        {{ request('herramienta_id') == $h->id ? 'selected' : '' }}>
                        {{ $h->codigo }} — {{ $h->nombre }}
                        (Disp: {{ $h->stock_disponible }} | {{ $h->estado }})
                    </option>
                    @endforeach
                </select>
            </div>

            <!-- Info dinámica de la herramienta seleccionada -->
            <div id="herr-info" style="display:none; background:rgba(13,148,136,.07); border:1px solid rgba(13,148,136,.3); border-radius:8px; padding:.75rem 1rem; margin-bottom:1.25rem; font-size:.88rem; transition:all .3s;">
                <i class="fa-solid fa-circle-info" style="color:var(--primary-color);"></i>
                <span id="herr-info-text" style="color:var(--text-muted); margin-left:.4rem;"></span>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:1rem; margin-bottom:1.25rem;">
                <div class="form-group">
                    <label style="color:var(--text-muted); font-size:.85rem; font-weight:600; display:block; margin-bottom:.5rem; text-transform:uppercase;">
                        Trabajador Responsable <span style="font-weight:400;">(Opcional)</span>
                    </label>
                    <select name="trabajador_id" id="sel_trabajador" class="form-control" style="width:100%;">
                        <option value="">Ninguno / No determinado</option>
                        @foreach($trabajadores as $t)
                            <option value="{{ $t->id }}" {{ request('trabajador_id') == $t->id ? 'selected' : '' }}>
                                {{ $t->dni }} - {{ $t->nombre }} {{ $t->apellidos }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label style="color:var(--text-muted); font-size:.85rem; font-weight:600; display:block; margin-bottom:.5rem; text-transform:uppercase;">
                        Monto Sanción (S/)
                    </label>
                    <input type="number" name="monto_sancion" class="form-control" min="0" step="0.10" value="0.00" placeholder="0.00">
                </div>
                @if(request()->has('vale_id'))
                    <input type="hidden" name="vale_id" value="{{ request('vale_id') }}">
                @endif
            </div>

            <!-- Tipo de incidencia como tarjetas -->
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label style="color:var(--text-muted); font-size:.85rem; font-weight:600; display:block; margin-bottom:.75rem; text-transform:uppercase; letter-spacing:.05em;">
                    Tipo de Incidencia
                </label>
                <div class="inc-tipo-grid">
                    <label class="inc-tipo-card" data-tipo="Dañado">
                        <input type="radio" name="estado_incidencia" value="Dañado" required>
                        <span class="tipo-icon">🔨</span>
                        <span class="tipo-label" style="color:var(--text-muted);">Roto / Dañado</span>
                    </label>
                    <label class="inc-tipo-card" data-tipo="Mantenimiento">
                        <input type="radio" name="estado_incidencia" value="Mantenimiento">
                        <span class="tipo-icon">🔧</span>
                        <span class="tipo-label" style="color:var(--text-muted);">Mantenimiento</span>
                    </label>
                    <label class="inc-tipo-card" data-tipo="Falla técnica">
                        <input type="radio" name="estado_incidencia" value="Falla técnica">
                        <span class="tipo-icon">⚠️</span>
                        <span class="tipo-label" style="color:var(--text-muted);">Falla técnica</span>
                    </label>
                    <label class="inc-tipo-card" data-tipo="Revision">
                        <input type="radio" name="estado_incidencia" value="Revision">
                        <span class="tipo-icon">🔍</span>
                        <span class="tipo-label" style="color:var(--text-muted);">Revisión</span>
                    </label>
                    <label class="inc-tipo-card" data-tipo="Perdido">
                        <input type="radio" name="estado_incidencia" value="Perdido">
                        <span class="tipo-icon">❓</span>
                        <span class="tipo-label" style="color:var(--text-muted);">Extraviado</span>
                    </label>
                </div>
            </div>

            <!-- Cantidad afectada -->
            <div class="form-group" style="margin-bottom:1.25rem;">
                <label style="color:var(--text-muted); font-size:.85rem; font-weight:600; display:block; margin-bottom:.5rem; text-transform:uppercase; letter-spacing:.05em;">
                    Unidades Afectadas
                </label>
                <input type="number" name="cantidad_afectada" id="cant_afectada" class="form-control" min="1" value="1"
                    style="width:120px;"
                    placeholder="1">
                <small style="color:var(--text-muted); font-size:.78rem; margin-top:.3rem; display:block;">
                    Se descontarán del stock disponible.
                </small>
            </div>

            <!-- Descripción / Notas -->
            <div class="form-group" style="margin-bottom:1.75rem;">
                <label style="color:var(--text-muted); font-size:.85rem; font-weight:600; display:block; margin-bottom:.5rem; text-transform:uppercase; letter-spacing:.05em;">
                    Descripción / Notas <span style="color:var(--text-muted); font-weight:400;">(Opcional)</span>
                </label>
                <textarea name="descripcion_incidencia" class="form-control" rows="3"
                    style="resize:vertical; font-size:.9rem;"
                    placeholder="Ej: Herramienta encontrada con el mango roto durante el turno de tarde..."></textarea>
            </div>

            <button type="submit" id="btnSubmit" class="btn btn-primary" style="width:100%; padding:1rem; font-size:1rem; position:relative; overflow:hidden;">
                <i class="fa-solid fa-flag"></i> Generar Reporte de Incidencia
            </button>
        </form>
        @endif
    </div>

    <!-- ──── PANEL DERECHO ──── -->
    <div style="display:flex; flex-direction:column; gap:1.25rem;">

        <!-- Protocolo -->
        <div class="card fade-in" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; animation-delay:.1s;">
            <h3 style="margin:0 0 1.25rem; color:var(--text-main);">
                <i class="fa-solid fa-book-open" style="color:#38BDF8;"></i> Protocolo de Incidencias
            </h3>
            <div style="display:flex; flex-direction:column; gap:.9rem;">
                <div style="display:flex; gap:.85rem; align-items:flex-start;">
                    <span style="font-size:1.4rem;">🔨</span>
                    <div>
                        <div style="color:var(--text-main); font-weight:600; font-size:.9rem;">Roto / Dañado</div>
                        <div style="color:var(--text-muted); font-size:.82rem; margin-top:.2rem;">La herramienta se rompió o falló. Se retira del stock disponible y pasa a "Dañado".</div>
                    </div>
                </div>
                <div style="display:flex; gap:.85rem; align-items:flex-start;">
                    <span style="font-size:1.4rem;">🔧</span>
                    <div>
                        <div style="color:var(--text-main); font-weight:600; font-size:.9rem;">Mantenimiento</div>
                        <div style="color:var(--text-muted); font-size:.82rem; margin-top:.2rem;">Requiere cambio de aceite, calibración o revisión periódica por horas de uso.</div>
                    </div>
                </div>
                <div style="display:flex; gap:.85rem; align-items:flex-start;">
                    <span style="font-size:1.4rem;">🔍</span>
                    <div>
                        <div style="color:var(--text-main); font-weight:600; font-size:.9rem;">Revisión</div>
                        <div style="color:var(--text-muted); font-size:.82rem; margin-top:.2rem;">Necesita ser inspeccionada antes de volver al uso. Se retiene temporalmente.</div>
                    </div>
                </div>
                <div style="display:flex; gap:.85rem; align-items:flex-start;">
                    <span style="font-size:1.4rem;">❓</span>
                    <div>
                        <div style="color:var(--text-main); font-weight:600; font-size:.9rem;">Extraviado / Perdido</div>
                        <div style="color:var(--text-muted); font-size:.82rem; margin-top:.2rem;">Solo si el trabajador declaró el extravío formalmente. Deja de contar en el stock.</div>
                    </div>
                </div>
            </div>
            <div style="margin-top:1.25rem; background:rgba(100,255,218,.05); border-left:3px solid #64FFDA; padding:.7rem 1rem; border-radius:4px; font-size:.82rem; color:var(--text-muted);">
                <i class="fa-solid fa-shield-halved" style="color:#64FFDA;"></i>
                Solo <strong style="color:#64FFDA;">Administradores</strong> y <strong style="color:#F59E0B;">Almaceneros</strong> pueden registrar incidencias.
            </div>
        </div>

        @php
        $herr_alertas = $herramientas->filter(function($h) {
            return in_array($h->estado, ['Dañado','Mantenimiento','Perdido','Falla técnica']);
        });
        @endphp
        @if($herr_alertas->isNotEmpty())
        <div class="card fade-in" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; animation-delay:.2s;">
            <h3 style="margin:0 0 1.1rem; color:var(--text-main); font-size:1rem;">
                <i class="fa-solid fa-circle-exclamation" style="color:#F59E0B;"></i> Herramientas Críticas
            </h3>
            <div style="display:flex; flex-direction:column; gap:.6rem; max-height:220px; overflow-y:auto;">
                @foreach($herr_alertas as $ha)
                @php
                    $colores = ['Dañado'=>'#EF4444','Mantenimiento'=>'#F59E0B','Perdido'=>'#8B5CF6','Falla técnica'=>'#EF4444'];
                    $color = $colores[$ha->estado] ?? '#8892B0';
                @endphp
                <div style="display:flex; justify-content:space-between; align-items:center; padding:.5rem .75rem; background:rgba(255,255,255,.03); border-radius:8px;">
                    <div style="font-size:.88rem; color:var(--text-main);">
                        <strong>{{ $ha->codigo }}</strong> — {{ $ha->nombre }}
                    </div>
                    <span style="color:{{ $color }}; background:{{ $color }}22; padding:2px 8px; border-radius:4px; font-size:.78rem; white-space:nowrap;">
                        {{ $ha->estado }}
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>

<!-- ──── HISTORIAL DE INCIDENCIAS ──── -->
<div class="card fade-in" style="background:var(--surface-color); border:1px solid var(--border-color); border-radius:14px; padding:1.5rem; margin-top:2rem;">
    <h3 style="margin:0 0 1.5rem; color:var(--text-main);">
        <i class="fa-solid fa-clock-rotate-left mr-1"></i> Historial de Sanciones e Incidencias
    </h3>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Herramienta</th>
                    <th>Tipo</th>
                    <th>Responsable</th>
                    <th style="text-align:right;">Sanción (S/)</th>
                    <th>Reportado por</th>
                </tr>
            </thead>
            <tbody>
                @if($incidencias->isEmpty())
                <tr><td colspan="6" style="text-align:center; color:var(--text-muted); padding:2rem;">No hay incidencias registradas.</td></tr>
                @else
                    @foreach($incidencias as $inc)
                    <tr>
                        <td><small style="color:var(--text-muted);">{{ \Carbon\Carbon::parse($inc->fecha)->format('d/m/Y H:i') }}</small></td>
                        <td>
                            <strong>{{ $inc->herramienta->nombre }}</strong><br>
                            <small style="color:var(--text-muted);">{{ $inc->herramienta->codigo }}</small>
                        </td>
                        <td><span class="badge {{ $inc->tipo }}">{{ $inc->tipo }}</span></td>
                        <td>
                            @if($inc->trabajador)
                                <i class="fa-solid fa-user" style="color:var(--text-muted);"></i> {{ $inc->trabajador->nombre }} {{ $inc->trabajador->apellidos }}
                            @else
                                <span style="color:var(--text-muted); font-style:italic;">No asignado</span>
                            @endif
                        </td>
                        <td style="text-align:right; font-weight:bold; color:{{ $inc->monto_sancion > 0 ? '#EF4444' : 'var(--text-muted)' }};">
                            S/ {{ number_format($inc->monto_sancion, 2) }}
                        </td>
                        <td><small>{{ $inc->usuario->nombre }}</small></td>
                    </tr>
                    @endforeach
                @endif
            </tbody>
        </table>
    </div>
</div>

<!-- Select2 & Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
$(document).ready(function() {
    // 1. Inicializar Select2
    $('#sel_herramienta').select2({ placeholder: "Buscar herramienta por código o nombre..." });
    $('#sel_trabajador').select2({ placeholder: "Ninguno / No determinado", allowClear: true });

    // 2. Comportamiento dinámico al seleccionar Herramienta
    $('#sel_herramienta').on('change', function() {
        let opt = $(this).find(':selected');
        let stock = opt.data('stock');
        let estado = opt.data('estado');

        if(stock !== undefined) {
            let inputCant = $('#cant_afectada');
            inputCant.attr('max', stock);
            
            // Si el stock es 0, advertir y poner en rojo
            if(stock <= 0) {
                inputCant.val(0).prop('disabled', true);
                $('#herr-info').css('display', 'flex').css('background', 'rgba(239,68,68,0.1)').css('border-color', '#EF4444');
                $('#herr-info-text').html('<strong style="color:#EF4444;">Sin stock disponible</strong>. Esta herramienta ya no tiene unidades operativas.');
                $('#btnSubmit').prop('disabled', true).css('opacity','0.5');
            } else {
                inputCant.val(1).prop('disabled', false);
                $('#herr-info').css('display', 'flex').css('background', 'rgba(13,148,136,.07)').css('border-color', 'rgba(13,148,136,.3)');
                $('#herr-info-text').html(`Stock actual: <strong>${stock}</strong> | Estado general: <strong>${estado}</strong>`);
                $('#btnSubmit').prop('disabled', false).css('opacity','1');
            }
        } else {
            $('#herr-info').hide();
            $('#btnSubmit').prop('disabled', false).css('opacity','1');
        }
    });

    // 3. Tarjetas de Tipo de Incidencia (Radio buttons visuales)
    $('.inc-tipo-card').on('click', function() {
        $('.inc-tipo-card').removeClass('selected');
        $(this).addClass('selected');
        $(this).find('input[type="radio"]').prop('checked', true);
    });

    // Disparar change al cargar para mostrar la info si viene pre-seleccionada
    if($('#sel_herramienta').val()) {
        $('#sel_herramienta').trigger('change');
    }
});
</script>
@endsection
