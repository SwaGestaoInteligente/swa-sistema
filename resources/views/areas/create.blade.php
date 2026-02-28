@extends('layouts.app')

@section('title', 'Nova área | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Nova área',
        'subtitle' => 'Areas comuns, tecnicas e de seguranca',
        'formAction' => route('condominios.context.areas.store', $condominio),
        'formView' => 'areas.form',
        'buttonLabel' => 'Salvar',
        'backRoute' => route('condominios.context.areas.index', $condominio),
    ])
@endsection
