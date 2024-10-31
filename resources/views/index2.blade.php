@extends('template')
@section('header')
    <meta property="og:url" content="{{ env('APP_URL') }}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('SITE_NAME', 'Lanceta HG') }}" />
    <meta property="og:description" content="{{ env('SITE_SLOGAN') }}" />
    <meta property="og:image" content="{{ asset('storage/logos/logolhg.png') }}" />
@endsection
@section('body')
    <!-- Banner de Cookies -->
    <div id="cookieConsent" class="cookie-banner fixed-bottom bg-dark text-white p-3 d-none" style="z-index: 9999;">
        <div class="container d-flex justify-content-between align-items-center">
            <div class="cookie-message">
                <span>Este sitio utiliza cookies para garantizar que obtenga la mejor experiencia en nuestro sitio web.
                    <a href="/politica-de-cookies" class="text-warning" target="_blank">Leer más</a>.
                </span>
            </div>
            <button id="acceptCookies" class="btn btn-warning">Aceptar</button>
        </div>
    </div>
    @include('partials.vistaitem')
@endsection
