@extends('layouts.app')

@section('title', 'Editar template | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar template',
        'subtitle' => $template->nome,
        'formAction' => route('condominios.context.templates.update', ['condominio' => $condominio, 'template' => $template]),
        'formMethod' => 'PUT',
        'formView' => 'templates.form',
        'buttonLabel' => 'Atualizar template',
        'backRoute' => route('condominios.context.templates.index', $condominio),
    ])
@endsection
