@extends('layouts.app')

@section('title', 'Editar pavimento | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar pavimento',
        'subtitle' => $pavimento->codigo,
        'formAction' => route('condominios.context.pavimentos.update', ['condominio' => $condominio, 'pavimento' => $pavimento]),
        'formMethod' => 'PUT',
        'formView' => 'pavimentos.form',
        'buttonLabel' => 'Atualizar',
        'backRoute' => route('condominios.context.pavimentos.index', $condominio),
    ])
@endsection
