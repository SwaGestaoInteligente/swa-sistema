@extends('layouts.app')

@section('title', 'Novo pavimento | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Novo pavimento',
        'subtitle' => 'Estrutura por bloco',
        'formAction' => route('condominios.context.pavimentos.store', $condominio),
        'formView' => 'pavimentos.form',
        'buttonLabel' => 'Salvar',
        'backRoute' => route('condominios.context.pavimentos.index', $condominio),
    ])
@endsection
