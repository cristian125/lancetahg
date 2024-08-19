<nav class="navbar navbar-expand-lg navbar-custom navbar-dark">
    <div class="container justify-content-center">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('storage/logos/logolhg.png') }}" alt="Logo" style="height: 30px;">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle active" href="#" id="navbarDropdownMenuLink" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        Categorías
                    </a>
                    <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="navbarDropdownMenuLink">
                        <!-- Contenido del dropdown de categorías aquí -->
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                <li class="nav-item me-3">
                    <form class="d-flex" role="search">
                        <input class="form-control me-2" type="search" placeholder="¿Qué producto está buscando?" aria-label="Search">
                        <button class="btn btn-outline-success" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </li>

                @if(Auth::check())
                    <!-- Mostrar el nombre del usuario si está autenticado -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill"></i> {{ Auth::user()->name }}
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="userDropdown" style="min-width: 300px;">
                            <form id="logout-form" action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-primary w-100">Cerrar Sesión</button>
                            </form>
                        </div>
                    </li>
                @else
                    <!-- Mostrar el formulario de inicio de sesión si no está autenticado -->
                    <li class="nav-item dropdown me-3">
                        <a class="nav-link dropdown-toggle" href="#" id="loginDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-person-fill"></i> Iniciar Sesión
                        </a>
                        <div class="dropdown-menu dropdown-menu-end p-4" aria-labelledby="loginDropdown" style="min-width: 300px;">
                            <form action="{{ route('login') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="email" class="form-label">Correo electrónico</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Contraseña</label>
                                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Iniciar Sesión</button>
                                <div class="mt-3">
                                    <a href="#">¿Olvidó su contraseña?</a> | <a href="#">Crear Cuenta</a>
                                </div>
                            </form>
                        </div>
                    </li>
                @endif

                <!-- Ícono de carrito de compras como enlace -->
                <li class="nav-item">
                    <a class="nav-link" href="{{ url('/carrito') }}">
                        <img src="{{ asset('storage/iconos/carrito.png') }}" alt="Carrito de Compras" style="height: 30px;">
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>
