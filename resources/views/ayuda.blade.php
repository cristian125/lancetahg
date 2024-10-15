@extends('template')

@section('body')

<div class="container my-5">
    <h1 class="text-center mb-4">Atención al Cliente</h1>
    
    <!-- Aquí se incrusta la página de tickets de Lanceta -->
    <iframe src="https://ticketsweb.lancetahg.com.mx/" width="100%" height="600" frameborder="0" allowfullscreen></iframe>
    
    <div class="mt-4 text-center">
        <p>Si tienes alguna consulta, por favor no dudes en ponerte en contacto con nosotros a través de nuestro sistema de tickets.</p>
    </div>
</div>

@endsection
