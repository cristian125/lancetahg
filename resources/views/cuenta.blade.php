@extends('template')
@section('header')
    <script>
        $(document).ready(function() {
            $('#addDireccion').on('click',function(){
                $('#modalAgregarDireccion').modal('show');
            });
            $('#frmAgregarDireccion #codigopostal').on('keyup', function (e) {
                let largo = $(this).val().trim().length;
                if(largo==5)
                {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cuenta.direccion.obtener') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            'codigopostal': $('#frmAgregarDireccion #codigopostal').val()
                        },
                        dataType: "json",
                        success: function (response) {
                            $('#frmAgregarDireccion #delegacion').html('');
                            $('#frmAgregarDireccion #estado').html('');

                            $.each(response, function (i, r) { 
                                let pais = r.codigo_pais;
                                let restringido = r.cp_restringido;
                                let municipio = r.municipio_ciudad;
                                let estado = r.provincia;
                                let zona = r.zona_local;

                                if($('#frmAgregarDireccion #delegacion option').length==0)
                                {
                                    $('#frmAgregarDireccion select#delegacion').append('<option value="'+municipio+'">'+municipio+'</option>');
                                }
                                else 
                                {
                                    $('#frmAgregarDireccion #delegacion option').each(function (index, element) {
                                        if(element.val()!==municipio)
                                        {
                                            $('#frmAgregarDireccion #delegacion').append('<option value="'+municipio+'">'+municipio+'</option>');
                                        }
                                    });
                                }
                                
                                if($('#frmAgregarDireccion #estado option').length==0)
                                {
                                    $('#frmAgregarDireccion select#estado').append('<option value="'+estado+'">'+estado+'</option>');
                                }
                                else 
                                {
                                    $('#frmAgregarDireccion #estado option').each(function (index, element) {
                                        if(element.val()!==estado)
                                        {
                                            $('#frmAgregarDireccion #estado').append('<option value="'+estado+'">'+estado+'</option>');
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
            $('#frmEditarDireccion #codigopostal').on('keyup', function (e) {
                let largo = $(this).val().trim().length;
                if(largo==5)
                {
                    $.ajax({
                        type: "POST",
                        url: "{{ route('cuenta.direccion.obtener') }}",
                        data: {
                            _token: "{{ csrf_token() }}",
                            'codigopostal': $('#frmEditarDireccion #codigopostal').val()
                        },
                        dataType: "json",
                        success: function (response) {
                            $('#frmEditarDireccion #delegacion').html('');
                            $('#frmEditarDireccion #estado').html('');

                            $.each(response, function (i, r) { 
                                let pais = r.codigo_pais;
                                let restringido = r.cp_restringido;
                                let municipio = r.municipio_ciudad;
                                let estado = r.provincia;
                                let zona = r.zona_local;

                                if($('#frmEditarDireccion #delegacion option').length==0)
                                {
                                    $('#frmEditarDireccion select#delegacion').append('<option value="'+municipio+'">'+municipio+'</option>');
                                }
                                else 
                                {
                                    $('#frmEditarDireccion #delegacion option').each(function (index, element) {
                                        if(element.val()!==municipio)
                                        {
                                            $('#frmEditarDireccion #delegacion').append('<option value="'+municipio+'">'+municipio+'</option>');
                                        }
                                    });
                                }
                                
                                if($('#frmEditarDireccion #estado option').length==0)
                                {
                                    $('#frmEditarDireccion select#estado').append('<option value="'+estado+'">'+estado+'</option>');
                                }
                                else 
                                {
                                    $('#frmEditarDireccion #estado option').each(function (index, element) {
                                        if(element.val()!==estado)
                                        {
                                            $('#frmEditarDireccion #estado').append('<option value="'+estado+'">'+estado+'</option>');
                                        }
                                    });
                                }
                            });
                        }
                    });
                }
            });
            $('#frmAgregarDireccion #btnCancelar').on('click', function(e) {
                e.preventDefault();
            });
            $('#frmAgregarDireccion #btnGuardar').on('click', function(e) {
                // e.preventDefault();
            });
            $('.btn-edit-address').on('click',function(){
                let id = $(this).parents('li:first').data('id');
                $('#frmEditarDireccion #id').val(id);
                $.ajax({
                    type: "POST",
                    url: "{{route('cuenta.direccion.obtenerdireccion')}}",
                    data: {
                        id: id,
                        _token: '{{ csrf_token() }}',
                    },
                    dataType: "json",
                    success: function (response) {
                        $(response[0]).each(function (i, r) {
                            // element == this
                            $('#frmEditarDireccion #nombre').val(this.nombre);
                            $('#frmEditarDireccion #calle').val(this.calle);
                            $('#frmEditarDireccion #int').val(this.no_int);
                            $('#frmEditarDireccion #ext').val(this.no_ext);
                            $('#frmEditarDireccion #entrecalles').val(this.entre_calles);
                            $('#frmEditarDireccion #colonia').val(this.colonia);                            
                            $('#frmEditarDireccion #codigopostal').val(this.codigo_postal);

                            $('#frmEditarDireccion #delegacion').append('<option value="'+this.municipio+'">'+this.municipio+'</option>');
                            $('#frmEditarDireccion #delegacion').change();

                            $('#frmEditarDireccion #estado').append('<option value="'+this.estado+'">'+this.estado+'</option>');
                            $('#frmEditarDireccion #estado').change();

                            $('#frmEditarDireccion #pais').append(this.pais);
                            $('#frmEditarDireccion #referencias').val(this.referencias);
                        });
                    }
                });

                $('#modalEditarDireccion').modal('show');
            });
            $('.btn-delete-address').on('click',function(){
                let element = $(this).parents('li:first');
                let id = element.data('id');
                $.ajax({
                    type: "POST",
                    url: "{{route('cuenta.direccion.eliminar')}}",
                    data: {
                        _token: "{{ csrf_token() }}",
                        'id': id,
                    },
                    dataType: "json",
                    success: function (response) {
                        if(response.message=='OK')
                        {
                            element.remove();
                        }
                    }
                });
            });
        });
    </script>
@endsection
@section('body')
    @foreach ($modal_files as $file)        
        @include('partials.modal.cuenta.'.$file)
    @endforeach

    <div class="container border rounded mt-3 p-3">        
        @if (session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        <div class="accordion" id="accordionAccount">
            @foreach ($accordion_files as $file)        
                @include('partials.accordion.cuenta.'.$file)
            @endforeach
            {{-- 
            <div class="accordion-item">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse"
                        data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        Accordion Item #3
                    </button>
                </h2>                
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                    data-bs-parent="#accordionAccount">
                    <div class="accordion-body">
                        <strong>This is the third item's accordion body.</strong> It is hidden by default, until the
                        collapse plugin adds the appropriate classes that we use to style each element. These classes
                        control the overall appearance, as well as the showing and hiding via CSS transitions. You can
                        modify any of this with custom CSS or overriding our default variables. It's also worth noting that
                        just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit
                        overflow.
                    </div>
                </div>                
            </div>
            --}}
        </div>
        <div class="row pt-3">

        </div>
        <hr />
    </div>
@endsection


