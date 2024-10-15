
@extends('template')
@section('header')
    <title>{{ env('SITE_NAME')}} - {!! $page->title !!}</title>
    <meta property="og:url" content="{{env('APP_URL')}}" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="{{ env('SITE_NAME')}} - {!! $page->title !!}" />
    <meta property="og:description" content="{{ env('SITE_SLOGAN') }}" />
    <meta property="og:image" content="{{ asset('storage/logos/logolhg.png') }}" />
    @if (File::exists(public_path('/css/\/'.$slug.'.css')))
    <link rel="stylesheet" href="{{ asset('/css/'.$slug.'.css') }}">
    @endif
    @if (File::exists(public_path('/js/\/'.$slug.'.js')))
    <link rel="stylesheet" href="{{ asset('/js/'.$slug.'.js') }}">
    @endif    
@endsection
@section('body')
<div class="container my-5">
    <div>
        {!! $page->content !!}
    </div>
</div>
@endsection
