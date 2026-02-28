@extends('layouts.app')

@section('title', 'Editar conflito de moradores | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar conflito',
        'subtitle' => $conflito->protocolo,
        'formAction' => route('condominios.context.conflitos.update', ['condominio' => $condominio, 'conflito' => $conflito]),
        'formMethod' => 'PUT',
        'formView' => 'conflitos-moradores.form',
        'buttonLabel' => 'Atualizar conflito',
        'backRoute' => route('condominios.context.conflitos.index', $condominio),
        'enctype' => 'multipart/form-data',
    ])
@endsection
