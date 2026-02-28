@extends('layouts.app')

@section('title', 'Nova unidade | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Nova unidade',
        'subtitle' => 'Vincule bloco e pavimento para organizar conflitos e moradores.',
        'formAction' => route('condominios.context.unidades.store', $condominio),
        'formView' => 'unidades.form',
        'buttonLabel' => 'Salvar unidade',
        'backRoute' => route('condominios.context.unidades.index', $condominio),
    ])
@endsection
