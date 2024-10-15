@extends('admin.template')

@section('header')
    <!-- Incluye Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endsection

@section('body')
    <!-- Sidebar -->
    <nav id="sidebar">
        <div class="p-3">
            <h4 class="mb-0 text-center">
                <i class="fas fa-user-shield"></i>
                <span id="logo-text">Administrador</span>
            </h4>
        </div>
        <ul class="nav flex-column">
            <!-- Aquí van los elementos del menú -->
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}"
                    class="nav-link {{ request()->is('admin/dashboard') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
            </li>
            @if(Auth::guard('admin')->user() && in_array(Auth::guard('admin')->user()->role, ['superusuario', 'editor']))
                <li class="nav-item">
                    <a href="{{ route('admin.items.index') }}"
                        class="nav-link {{ request()->is('admin/items') ? 'active' : '' }}">
                        <i class="fas fa-box"></i>
                        <span>Items</span>
                    </a>
                </li>

                
                            
            @endif
            @if(Auth::guard('admin')->user() && Auth::guard('admin')->user()->role === 'superusuario')
                <li class="nav-item">
                    <a href="{{ route('admin.manage.admins') }}"
                        class="nav-link {{ request()->is('admin/manage-admins') ? 'active' : '' }}">
                        <i class="fas fa-users"></i>
                        <span>Usuarios</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('admin.shipping_methods') }}"
                        class="nav-link {{ request()->is('admin/shipping-methods') ? 'active' : '' }}">
                        <i class="fas fa-shipping-fast"></i>
                        <span>Métodos de Envío</span>
                    </a>
                </li>
            @endif
            <li class="nav-item">
                <a href="#" class="nav-link">
                    <i class="fas fa-cogs"></i>
                    <span>Configuración</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="{{ route('admin.logout') }}" class="nav-link"
                    onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Cerrar Sesión</span>
                </a>
            </li>
        </ul>
    </nav>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <!-- Botón para colapsar/expandir el sidebar -->
        <button class="btn btn-outline-light me-2" id="sidebarCollapse">
            <i class="fas fa-bars"></i>
        </button>
        <a class="navbar-brand" href="#">Panel</a>
        <!-- Botón de colapso para la navegación en dispositivos móviles -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
            data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false"
            aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Enlaces de navegación -->
        <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
            <ul class="navbar-nav">
                @if (auth()->check())

                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user-circle"></i> {{ auth()->user()->name }}</a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="#"><i class="fas fa-user-circle"></i> Invitado</a>
                    </li>
                @endif
                <li class="nav-item">
                    <a class="nav-link" href="#"><i class="fas fa-cogs"></i> Configuración</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="#"
                        onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        <i class="fas fa-sign-out-alt"></i> Cerrar Sesión
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                        @csrf
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>



    <!-- Contenido Principal -->
    <div id="content">
        <div class="content-wrapper">
            @yield('content')
        </div>
    </div>

    <!-- Formulario de Cierre de Sesión -->
    <form id="logout-form" action="{{ route('admin.logout') }}" method="POST" style="display: none;">
        @csrf
    </form>

    <!-- Scripts de jQuery y Bootstrap -->
    <script>
        // Funcionalidad para colapsar/expandir el sidebar y cambiar el texto del logo
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar').toggleClass('collapsed');
            $('#content').toggleClass('collapsed');
            $('.navbar').toggleClass('collapsed');

            // Cambiar el texto del logo
            var logoText = $('#logo-text');
            if ($('#sidebar').hasClass('collapsed')) {
                logoText.text('LHG');
            } else {
                logoText.text('Administrador');
            }
        });
    </script>

@endsection
