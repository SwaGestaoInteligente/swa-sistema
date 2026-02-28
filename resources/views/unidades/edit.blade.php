@extends('layouts.app')

@section('title', 'Editar unidade | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar unidade',
        'subtitle' => (string) $unidade->numero,
        'formAction' => route('condominios.context.unidades.update', ['condominio' => $condominio, 'unidade' => $unidade]),
        'formMethod' => 'PUT',
        'formView' => 'unidades.form',
        'buttonLabel' => 'Atualizar unidade',
        'backRoute' => route('condominios.context.unidades.index', $condominio),
    ])
@endsection
