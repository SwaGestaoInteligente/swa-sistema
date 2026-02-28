@extends('layouts.app')

@section('title', 'Novo template | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Novo template de checklist',
        'subtitle' => 'Monte a lista padrao que sera aplicada nas vistorias.',
        'formAction' => route('condominios.context.templates.store', $condominio),
        'formView' => 'templates.form',
        'buttonLabel' => 'Salvar template',
        'backRoute' => route('condominios.context.templates.index', $condominio),
    ])
@endsection
