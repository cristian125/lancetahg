@extends('template')

@section('header')
    <script>
        $(document).ready(function() {
            $('#addDireccion').on('click', function() {
                $('#modalAgregarDireccion').modal('show');
            });
            $('#frmAgregarDireccion #btnCancelar').on('click',function(e){
                e.preventDefault();
            });
             
            $('#frmAgregarDireccion #codigopostal').on('keyup', function(e) {
                $('#frmAgregarDireccion .alert').remove();
                let largo = $(this).val().trim().length;
                let codigopostal = $('#frmAgregarDireccion #codigopostal').val();
                if (largo > 5) {
                    codigopostal = codigopostal.substring(0, 5);
                    $('#frmAgregarDireccion #codigopostal').val(codigopostal);
                    return;
                }

                
                if (largo >= 5) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cuenta.direccion.obtener') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            'codigopostal': codigopostal
                        },
                        dataType: "json",
                        success: function(response) {
                            $('#frmAgregarDireccion #delegacion').html('');
                            $('#frmAgregarDireccion #estado').html('');
                            if (response.length == 0) {
                                $('#frmAgregarDireccion').prepend(
                                    '<div class="alert alert-danger alert-dismissible" role="alert">No se encontro el código postal capturado ' +
                                    codigopostal +
                                    '.<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>'
                                    )
                                return;
                            }

                            $.each(response, function(i, r) {
                                let municipio = r.municipio_ciudad;
                                let estado = r.provincia;

                                if ($('#frmAgregarDireccion #delegacion option')
                                    .length == 0) {
                                    $('#frmAgregarDireccion select#delegacion').append(
                                        '<option value="' + municipio + '">' +
                                        municipio + '</option>');
                                }

                                if ($('#frmAgregarDireccion #estado option').length ==
                                    0) {
                                    $('#frmAgregarDireccion select#estado').append(
                                        '<option value="' + estado + '">' + estado +
                                        '</option>');
                                }
                            });
                        }
                    });
                }
            });

            $('#frmEditarDireccion #codigopostal').on('keyup', function(e) {
                let largo = $(this).val().trim().length;
                if (largo == 5) {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cuenta.direccion.obtener') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            'codigopostal': $('#frmEditarDireccion #codigopostal').val()
                        },
                        dataType: "json",
                        success: function(response) {
                            $('#frmEditarDireccion #delegacion').html('');
                            $('#frmEditarDireccion #estado').html('');
                            $.each(response, function(i, r) {
                                let municipio = r.municipio_ciudad;
                                let estado = r.provincia;

                                if ($('#frmEditarDireccion #delegacion option')
                                    .length == 0) {
                                    $('#frmEditarDireccion select#delegacion').append(
                                        '<option value="' + municipio + '">' +
                                        municipio + '</option>');
                                }

                                if ($('#frmEditarDireccion #estado option').length ==
                                    0) {
                                    $('#frmEditarDireccion select#estado').append(
                                        '<option value="' + estado + '">' + estado +
                                        '</option>');
                                }
                            });
                        }
                    });
                }
            });

            $('.btn-edit-address').on('click', function() {
                let id = $(this).parents('li:first').data('id');
                $('#frmEditarDireccion #id').val(id);
                $.ajax({
                    type: "POST",
                    url: "{{ route('cuenta.direccion.obtenerdireccion') }}",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}'
                    },
                    dataType: "json",
                    success: function(response) {
                        let address = response[0];
                        $('#frmEditarDireccion #nombre').val(address.nombre);
                        $('#frmEditarDireccion #calle').val(address.calle);
                        $('#frmEditarDireccion #int').val(address.no_int);
                        $('#frmEditarDireccion #ext').val(address.no_ext);
                        $('#frmEditarDireccion #colonia').val(address.colonia);
                        $('#frmEditarDireccion #codigopostal').val(address.codigo_postal);
                        $('#frmEditarDireccion #delegacion').append('<option value="' + address
                            .municipio + '">' + address.municipio + '</option>');
                        $('#frmEditarDireccion #estado').append('<option value="' + address
                            .estado + '">' + address.estado + '</option>');
                        $('#frmEditarDireccion #pais').val(address.pais);
                        $('#frmEditarDireccion #referencias').val(address.referencias);
                    }
                });
                $('#modalEditarDireccion').modal('show');
            });

            $('.btn-delete-address').on('click', function() {
                let element = $(this).parents('li:first');
                let id = element.data('id');
                $.ajax({
                    type: "POST",
                    url: "{{ route('cuenta.direccion.eliminar') }}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        'id': id
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.message == 'OK') {
                            element.remove();
                        }
                    }
                });
            });
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {

            const sectionToOpen = "{{ $sectionToOpen }}";

            if (sectionToOpen) {

                const accordionButton = document.querySelector(`[data-bs-target="#${sectionToOpen}"]`);
                const accordionCollapse = document.querySelector(`#${sectionToOpen}`);

                if (accordionButton && accordionCollapse) {
                    accordionCollapse.classList.add('show'); 
                    accordionButton.setAttribute('aria-expanded',
                        'true'); 
                }
            }
        });
    </script>
    <style>
        #modalAgregarDireccion{
            margin-top:5em;
        }
    </style>
@endsection
@section('body')
    @foreach ($modal_files as $file)
        @include('partials.modal.cuenta.' . $file)
    @endforeach


    <div class="container my-5">

        <div class="card shadow-lg border-0 rounded">
            <div class="card-body p-5">
                <!-- Sección de encabezado de la cuenta -->
                <div class="row mb-4">
                    <div class="col-12 text-center">
                        <h2 class="bi bi-person-circle"> Cuenta</h2>
                        <p class="text-muted">Administre su información personal, direcciones y datos desde aquí.</p>
                    </div>
                </div>

                <!-- Mensajes de estado -->
                <div class="row mb-4">
                    <div class="col-12">
                        @if (session('error'))
                            <div class="alert alert-danger">{{ session('error') }}</div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success">{{ session('status') }}</div>
                        @endif
                    </div>
                </div>

                <!-- Contenedor de la información de la cuenta -->
                <div class="row">
                    <!-- Sección principal del acordeón -->
                    <div class="col-lg-12">
                        <div class="accordion shadow-sm" id="accordionAccount">
                            <!-- Aquí se incluyen los acordeones personalizados -->
                            @foreach ($accordion_files as $file)
                                @include('partials.accordion.cuenta.' . $file)
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Pie de página de la cuenta -->
            <div class="card-footer bg-light text-center">
                <hr>
                <p class="text-muted small mb-0">© {{ date('Y') }} LancetaHG®</p>
            </div>
        </div>
    </div>
@endsection
