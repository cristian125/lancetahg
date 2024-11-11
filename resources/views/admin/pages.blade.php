@extends('admin.template')

@section('content')
<div class="container mt-5">
    <!-- Encabezado del editor -->
    <div class="d-flex justify-content-between align-items-center mb-5">
        <h2 class="display-4 fw-bold">Editor de Contenidos</h2>
        <!-- Botón estilizado para crear una nueva página -->
        <a href="{{ route('admin.pages.create') }}" class="btn btn-success btn-lg shadow-sm">
            <i class="fas fa-plus-circle me-2"></i> Añadir Nueva Página
        </a>
    </div>

    <!-- Mostrar mensajes de éxito o error -->
    @if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @elseif (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif


    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        @foreach ($pages as $page)
        <div class="col">
            <div class="card h-100 shadow-sm border-0 rounded-lg card-hover-effect">
                <div class="card-body d-flex flex-column justify-content-between p-4">

                    <h5 class="card-title text-dark text-uppercase fw-bold mb-3">{{ $page->title }}</h5>

                 
                    <p class="card-text text-muted mb-4">
                        <strong>Slug:</strong> {{ $page->slug }}
                    </p>


                    <div class="mt-auto">
                        <a href="{{ route('admin.editor', $page->id) }}" class="btn btn-primary w-100 text-uppercase py-2">
                            <i class="fas fa-edit me-2"></i> Editar Página
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Ocultar mensajes de éxito o error después de 3 segundos
        var successMessage = document.querySelector('.alert-success');
        var errorMessage = document.querySelector('.alert-danger');

        if (successMessage) {
            setTimeout(function() {
                successMessage.style.display = 'none';
            }, 3000);
        }

        if (errorMessage) {
            setTimeout(function() {
                errorMessage.style.display = 'none';
            }, 3000);
        }
    });
</script>

<style>
    /* Estilo para el botón de crear página */
    .btn-success {
        background-color: #28a745;
        border: none;
        transition: background-color 0.3s ease-in-out, transform 0.2s ease;
    }
    .btn-success:hover {
        background-color: #218838;
        transform: translateY(-2px);
    }

    /* Tarjetas con bordes suaves y efecto hover */
    .card-hover-effect {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        border-radius: 10px;
    }
    .card-hover-effect:hover {
        transform: translateY(-10px);
        box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.1);
    }

    /* Botones de edición estilizados */
    .btn-primary {
        background-color: #007bff;
        border: none;
        transition: background-color 0.3s ease-in-out, transform 0.2s ease;
    }
    .btn-primary:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    /* Tipografía de títulos y slug */
    .card-title {
        font-size: 1.25rem;
        font-weight: 700;
    }

    .display-4 {
        font-weight: 800;
        letter-spacing: 1px;
        color: #343a40;
    }

    .card-text {
        font-size: 0.9rem;
        color: #6c757d;
    }

    /* Estilos de alerta (éxito o error) */
    .alert {
        border-radius: 10px;
    }
</style>

<style>
    body {
        background: rgb(195,195,195);
        background: linear-gradient(90deg, rgba(195,195,195,1) 4%, rgba(227,227,227,1) 79%);
    }
</style>
@endsection
