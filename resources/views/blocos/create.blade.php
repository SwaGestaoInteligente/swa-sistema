@extends('layouts.app')

@section('title', 'Novo bloco | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Novo bloco',
        'subtitle' => 'Estrutura fisica do condominio',
        'formAction' => route('condominios.context.blocos.store', $condominio),
        'formView' => 'blocos.form',
        'buttonLabel' => 'Salvar',
        'backRoute' => route('condominios.context.blocos.index', $condominio),
    ])
@endsection
