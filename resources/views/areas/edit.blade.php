@extends('layouts.app')

@section('title', 'Editar área | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar área',
        'subtitle' => $area->codigo,
        'formAction' => route('condominios.context.areas.update', ['condominio' => $condominio, 'area' => $area]),
        'formMethod' => 'PUT',
        'formView' => 'areas.form',
        'buttonLabel' => 'Atualizar',
        'backRoute' => route('condominios.context.areas.index', $condominio),
    ])
@endsection
