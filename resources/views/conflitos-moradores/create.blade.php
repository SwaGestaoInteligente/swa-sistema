@extends('layouts.app')

@section('title', 'Novo conflito de moradores | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Novo conflito de moradores',
        'subtitle' => 'Registre o fato com clareza e anexe evidencias se necessario.',
        'formAction' => route('condominios.context.conflitos.store', $condominio),
        'formView' => 'conflitos-moradores.form',
        'buttonLabel' => 'Salvar conflito',
        'backRoute' => route('condominios.context.conflitos.index', $condominio),
        'enctype' => 'multipart/form-data',
    ])
@endsection
