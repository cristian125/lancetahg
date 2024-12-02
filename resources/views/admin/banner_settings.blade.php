@extends('admin.template')

@section('content')
    <div class="container">
        <h1>Configuración de Banners</h1>

        <!-- Mensajes de Éxito y Error -->
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        <div class="row">
            <!-- Sección para Banner Desktop -->
            <div class="col-md-6">
                <h3>Banner Desktop</h3>
                <form action="{{ route('admin.upload_banner') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="device" value="desktop">
                    <div class="mb-3">
                        <label for="banner_image_desktop" class="form-label">Selecciona Imagen para Desktop</label>
                        <input class="form-control" type="file" id="banner_image_desktop" name="banner_image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Subir Banner Desktop</button>
                </form>

                <!-- Mostrar Banners Activos Desktop -->
                @if($bannerImages->where('device', 'desktop')->count() > 0)
                    <h4 class="mt-4">Banners Desktop Activos</h4>
                    <ul class="list-group">
                        @foreach($bannerImages->where('device', 'desktop') as $banner)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner Desktop" width="200">
                                <div>
                                    <!-- Botón para Activar/Desactivar -->
                                    <form action="{{ route('admin.toggle_banner', $banner->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $banner->active ? 'secondary' : 'success' }}">
                                            {{ $banner->active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    <!-- Botón para Eliminar -->
                                    <form action="{{ route('admin.delete_banner', $banner->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este banner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>

            <!-- Sección para Banner Mobile -->
            <div class="col-md-6">
                <h3>Banner Móvil</h3>
                <form action="{{ route('admin.upload_banner') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="device" value="mobile">
                    <div class="mb-3">
                        <label for="banner_image_mobile" class="form-label">Selecciona Imagen para Móvil</label>
                        <input class="form-control" type="file" id="banner_image_mobile" name="banner_image" accept="image/*" required>
                    </div>
                    <button type="submit" class="btn btn-primary">Subir Banner Móvil</button>
                </form>

                <!-- Mostrar Banners Activos Mobile -->
                @if($bannerImages->where('device', 'mobile')->count() > 0)
                    <h4 class="mt-4">Banners Móviles Activos</h4>
                    <ul class="list-group">
                        @foreach($bannerImages->where('device', 'mobile') as $banner)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <img src="{{ asset('storage/' . $banner->image_path) }}" alt="Banner Móvil" width="200">
                                <div>
                                    <!-- Botón para Activar/Desactivar -->
                                    <form action="{{ route('admin.toggle_banner', $banner->id) }}" method="POST" style="display:inline;">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $banner->active ? 'secondary' : 'success' }}">
                                            {{ $banner->active ? 'Desactivar' : 'Activar' }}
                                        </button>
                                    </form>
                                    <!-- Botón para Eliminar -->
                                    <form action="{{ route('admin.delete_banner', $banner->id) }}" method="POST" style="display:inline;" onsubmit="return confirm('¿Estás seguro de eliminar este banner?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">Eliminar</button>
                                    </form>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    </div>
    <!-- Estilos adicionales -->
    <style>
        .image-container {
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
        }
    </style>
@endsection
