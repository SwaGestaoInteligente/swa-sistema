@extends('layouts.app')

@section('title', 'Editar condomínio | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar condomínio',
        'subtitle' => $condominio->codigo,
        'eyebrow' => 'Cadastro raiz',
        'formAction' => route('condominios.update', $condominio),
        'formMethod' => 'PUT',
        'formView' => 'condominios.form',
        'buttonLabel' => 'Atualizar',
        'backRoute' => route('condominios.index'),
    ])
@endsection
