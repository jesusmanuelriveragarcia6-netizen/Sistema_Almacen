<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Almacén Inteligente</title>
    <meta name="description" content="Sistema de gestión de almacén. Inicia sesión para continuar.">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h2><i class="fa-solid fa-boxes-stacked" style="color: var(--primary-color);"></i> Almacén Inteligente</h2>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}
                </div>
            @endif

            @if (session('status'))
                <div class="alert" style="background-color: #D1FAE5; color: #047857; border: 1px solid #34D399;">
                    <i class="fa-solid fa-check-circle"></i> {{ session('status') }}
                </div>
            @endif

            <form action="{{ route('login') }}" method="POST" autocomplete="off">
                @csrf

                <div class="form-group">
                    <label for="usuario">Usuario</label>
                    <input type="text"
                           id="usuario"
                           name="usuario"
                           class="form-control"
                           required
                           autofocus
                           autocomplete="new-password"
                           readonly
                           onfocus="this.removeAttribute('readonly');"
                           value="{{ old('usuario') }}">
                </div>
                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-control"
                           required
                           autocomplete="new-password"
                           readonly
                           onfocus="this.removeAttribute('readonly');">
                </div>
                <button type="submit" class="btn btn-primary" id="btn-login">
                    Iniciar Sesión <i class="fa-solid fa-arrow-right ml-1"></i>
                </button>
            </form>

            <div style="text-align: center; margin-top: 1.5rem;">
                <p style="color: var(--text-muted); font-size: 0.8rem;">
                    <i class="fa-solid fa-shield-halved"></i>
                    Sistema de acceso restringido. Solo personal autorizado.
                </p>
            </div>
        </div>
    </div>
</body>
</html>
