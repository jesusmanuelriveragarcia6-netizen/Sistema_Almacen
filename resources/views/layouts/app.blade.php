<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>@yield('title', 'Almacén Inteligente')</title>
    <link rel="stylesheet" href="{{ asset('css/style.css') }}?v={{ time() }}">
    <link rel="stylesheet" href="{{ asset('css/table.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
</head>
<body>
<div class="wrapper">
    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand">
            <i class="fa-solid fa-boxes-stacked"></i> Almacén
        </div>
        <nav class="sidebar-nav">
            <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie" style="width: 20px;"></i> Dashboard
            </a>
            <a href="{{ route('cortex.index') }}" class="nav-link {{ request()->routeIs('cortex.index') ? 'active' : '' }}">
                <i class="fa-solid fa-brain-circuit" style="width: 20px;"></i> Cortex Assistant
            </a>
            <a href="{{ route('herramientas.index') }}" class="nav-link {{ request()->is('herramientas') || request()->is('herramientas/*') && !request()->is('herramientas/ubicaciones') ? 'active' : '' }}">
                <i class="fa-solid fa-wrench" style="width: 20px;"></i> Catálogo
            </a>
            <a href="{{ route('herramientas.ubicaciones') }}" class="nav-link {{ request()->is('herramientas/ubicaciones') ? 'active' : '' }}">
                <i class="fa-solid fa-warehouse" style="width: 20px;"></i> Ubicaciones
            </a>
            <a href="{{ route('almacenes.index') }}" class="nav-link {{ request()->is('almacenes*') ? 'active' : '' }}">
                <i class="fa-solid fa-boxes-stacked" style="width: 20px;"></i> Almacenes
            </a>
            <a href="{{ route('trabajadores.index') }}" class="nav-link {{ request()->is('trabajadores*') && !request()->is('*inactivos') ? 'active' : '' }}">
                <i class="fa-solid fa-users-gear" style="width: 20px;"></i> Trabajadores
            </a>

            @if(Auth::user()->rol !== 'Supervisor')
            <a href="{{ route('vales.index') }}" class="nav-link {{ request()->is('vales') || request()->is('vales/*/edit') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice" style="width: 20px;"></i> Vales de Salida
            </a>
            <a href="{{ url('vales/historial') }}" class="nav-link {{ request()->is('vales/historial') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open" style="width: 20px;"></i> Registro de Vales
            </a>
            @else
            <!-- El Supervisor puede ver reportes (acceso de solo lectura) -->
            <a href="{{ route('vales.index') }}" class="nav-link {{ request()->is('vales') ? 'active' : '' }}">
                <i class="fa-solid fa-file-invoice" style="width: 20px;"></i> Vales de Salida
            </a>
            <a href="{{ url('vales/historial') }}" class="nav-link {{ request()->is('vales/historial') ? 'active' : '' }}">
                <i class="fa-solid fa-folder-open" style="width: 20px;"></i> Registro de Vales
            </a>
            @endif

            @if(in_array(Auth::user()->rol, ['Administrador', 'Supervisor']))
            <div class="nav-divider" style="border-top: 1px solid var(--border-color); margin: 0.5rem 1rem;"></div>
            <a href="{{ route('reportes.index') }}" class="nav-link {{ request()->is('reportes*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-chart-column" style="width: 20px;"></i> Reportes
            </a>
            @endif

            @if(Auth::user()->rol === 'Administrador')
            <a href="{{ url('usuarios') }}" class="nav-link {{ request()->is('usuarios*') ? 'active' : '' }}">
                <i class="fa-solid fa-users-cog" style="width: 20px;"></i> Gestión de Usuarios
            </a>
            @endif
        </nav>
    </aside>

    <!-- Main Content wrapper -->
    <div class="main-content">
        <!-- Header Principal -->
        <header class="main-header premium-header">
            <div class="header-left">
                <span class="system-time text-muted small" id="live-clock"></span>
            </div>
            <div class="header-right">
                <div class="user-profile-dropdown">
                    @php
                    $rolIcon = [
                        'Administrador' => 'fa-user-shield',
                        'Almacenero'    => 'fa-user-gear',
                        'Supervisor'    => 'fa-user-tie',
                    ];
                    $icon = $rolIcon[Auth::user()->rol] ?? 'fa-user';
                    @endphp
                    <div class="user-info-chip">
                        <div class="user-avatar">
                            <i class="fa-solid {{ $icon }}"></i>
                        </div>
                        <div class="user-meta">
                            <span class="user-name">{{ Auth::user()->nombre }}</span>
                            <span class="user-role">{{ Auth::user()->rol }}</span>
                        </div>
                    </div>
                    
                    <form action="{{ route('logout') }}" method="POST" class="logout-form" onsubmit="return confirm('¿Finalizar sesión segura?');">
                        @csrf
                        <button type="submit" class="logout-btn" title="Desconexión Segura">
                            <i class="fa-solid fa-power-off"></i>
                        </button>
                    </form>
                </div>
            </div>
        </header>

        <script>
            function updateClock() {
                const now = new Date();
                document.getElementById('live-clock').innerText = now.toLocaleString('es-ES', { 
                    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
                    hour: '2-digit', minute: '2-digit'
                });
            }
            setInterval(updateClock, 1000);
            updateClock();
        </script>


        <!-- Page Content -->
        <main class="page-content">
            @yield('content')
        </main> <!-- End Page Content -->
    </div> <!-- /main-content -->
</div> <!-- /wrapper -->

<script>
// Sistema de alertas global con SweetAlert2
document.addEventListener('DOMContentLoaded', function() {
    @if(session('exito') || request()->has('exito'))
        Swal.fire({ icon: 'success', title: '¡Éxito!', text: 'Operación realizada correctamente.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    @endif
    @if(session('eliminado') || request()->has('eliminado'))
        Swal.fire({ icon: 'warning', title: 'Eliminado', text: 'El registro ha sido eliminado (borrado lógico).', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    @endif
    @if(session('modificado') || request()->has('modificado'))
        Swal.fire({ icon: 'success', title: 'Actualizado', text: 'Los datos se han actualizado correctamente.', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true });
    @endif
    @if(session('error') || request()->has('error'))
        Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al procesar la solicitud.', toast: true, position: 'top-end', showConfirmButton: false, timer: 4000, timerProgressBar: true });
    @endif
});
</script>

<script src="{{ asset('js/main.js') }}"></script>
</body>
</html>
