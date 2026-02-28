@extends('layouts.app')

@section('title', 'Editar bloco | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar bloco',
        'subtitle' => $bloco->codigo,
        'formAction' => route('condominios.context.blocos.update', ['condominio' => $condominio, 'bloco' => $bloco]),
        'formMethod' => 'PUT',
        'formView' => 'blocos.form',
        'buttonLabel' => 'Atualizar',
        'backRoute' => route('condominios.context.blocos.index', $condominio),
    ])
@endsection
