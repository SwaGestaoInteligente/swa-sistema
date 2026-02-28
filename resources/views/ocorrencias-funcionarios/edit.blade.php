@extends('layouts.app')

@section('title', 'Editar ocorrência de funcionário | SWA')
@section('hide_nav', '1')

@section('content')
    @include('partials.form-shell', [
        'title' => 'Editar ocorrência',
        'subtitle' => $ocorrencia->protocolo,
        'formAction' => route('condominios.context.ocorrencias.update', ['condominio' => $condominio, 'ocorrencia' => $ocorrencia]),
        'formMethod' => 'PUT',
        'formView' => 'ocorrencias-funcionarios.form',
        'buttonLabel' => 'Atualizar ocorrência',
        'backRoute' => route('condominios.context.ocorrencias.index', $condominio),
        'enctype' => 'multipart/form-data',
    ])
@endsection
