@extends('admin.template')

@section('content')
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <!-- Card contenedor para el formulario -->
            <div class="card shadow-sm border-0 rounded-lg">
                <div class="card-header bg-primary text-white text-center">
                    <h2 class="mb-0">Crear nueva página</h2>
                </div>
                <div class="card-body p-4">
                    <!-- Mostrar errores de validación -->
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>- {{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <!-- Formulario para crear una nueva página -->
                    <form action="{{ route('admin.pages.store') }}" method="POST">
                        @csrf

                        <!-- Campo de Título -->
                        <div class="form-group mb-4">
                            <label for="title" class="form-label">Título de la página</label>
                            <input type="text" id="title" name="title" class="form-control" placeholder="Escribe el título de la página" required>
                        </div>

                        <!-- Campo de Slug -->
                        <div class="form-group mb-4">
                            <label for="slug" class="form-label">Slug</label>
                            <input type="text" id="slug" name="slug" class="form-control" placeholder="Escribe el slug (URL)" required>
                            <small class="form-text text-muted">El slug es la parte de la URL que identifica a la página. Debe ser único y sin espacios.</small>
                        </div>

                        <!-- Botón de enviar y cancelar -->
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-success px-4">Crear página</button>
                            <a href="{{ route('admin.pages.list') }}" class="btn btn-secondary px-4">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Script para ocultar los mensajes de error después de 3 segundos y generar el slug automáticamente -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ocultar mensajes de error después de 3 segundos
        var alert = document.querySelector('.alert');
        if (alert) {
            setTimeout(function() {
                alert.classList.add('fade');
                setTimeout(function() {
                    alert.style.display = 'none';
                }, 500);
            }, 3000);
        }

        // Generación automática del slug
        document.getElementById('title').addEventListener('input', function() {
            var slugInput = document.getElementById('slug');
            var title = this.value;

            // Generar el slug reemplazando espacios por guiones y convirtiendo a minúsculas
            var slug = title.toLowerCase().trim()
                .replace(/[^a-z0-9\s-]/g, '')   // Eliminar caracteres especiales
                .replace(/\s+/g, '-')           // Reemplazar espacios por guiones
                .replace(/-+/g, '-');           // Reemplazar múltiples guiones por uno solo

            slugInput.value = slug;
        });
    });
</script>

<style>
    /* Estilo adicional para darle un mejor aspecto */
    .form-control {
        border-radius: 8px;
        padding: 10px;
    }

    .btn-success {
        background-color: #28a745;
        border: none;
    }

    .btn-secondary {
        background-color: #6c757d;
        border: none;
    }

    .card {
        border-radius: 12px;
    }

    .card-header {
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
    }
</style>
@endsection