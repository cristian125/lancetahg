@extends('template')
@section('header')
<script>
    $(document).ready(function() {
        // Cuando se abre el modal
        $('#enviosModal').on('show.bs.modal', function () {
            $('nav').hide();  // Oculta el navbar
        });

        // Cuando se cierra el modal
        $('#enviosModal').on('hidden.bs.modal', function () {
            $('nav').show();  // Muestra el navbar nuevamente
        });
    });
</script>
@endsection
@section('body')
@include('partials.envios')
@endsection

