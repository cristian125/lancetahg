@extends('template')
@section('header')
<script>
    $(document).ready(function() {
        $('#enviosModal').on('show.bs.modal', function () {
            $('nav').hide();  
        });


        $('#enviosModal').on('hidden.bs.modal', function () {
            $('nav').show();  
        });
    });
</script>
@endsection
@section('body')
@include('partials.envios')
@endsection

