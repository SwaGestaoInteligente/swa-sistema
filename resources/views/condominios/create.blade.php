@extends('layouts.app')

@section('title', 'Novo condomínio | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Novo condomínio',
        'subtitle' => 'Cadastro base do tenant',
        'eyebrow' => 'Cadastro raiz',
        'formAction' => route('condominios.store'),
        'formView' => 'condominios.form',
        'buttonLabel' => 'Salvar',
        'backRoute' => route('condominios.index'),
    ])
@endsection
