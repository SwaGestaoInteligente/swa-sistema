@extends('layouts.app')

@section('title', 'Nova vistoria | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Nova vistoria',
        'subtitle' => 'Crie a vistoria, aplique template e siga para o modo campo.',
        'formAction' => route('condominios.context.vistorias.store', $condominio),
        'formView' => 'vistorias.form',
        'buttonLabel' => 'Salvar e abrir detalhes',
        'backRoute' => route('condominios.context.vistorias.index', $condominio),
    ])
@endsection
