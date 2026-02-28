@extends('layouts.app')

@section('title', 'Nova ocorrência de funcionário | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Nova ocorrência de funcionário',
        'subtitle' => 'Formalize o registro com cargo, relato e medida aplicada.',
        'formAction' => route('condominios.context.ocorrencias.store', $condominio),
        'formView' => 'ocorrencias-funcionarios.form',
        'buttonLabel' => 'Salvar ocorrência',
        'backRoute' => route('condominios.context.ocorrencias.index', $condominio),
        'enctype' => 'multipart/form-data',
    ])
@endsection
