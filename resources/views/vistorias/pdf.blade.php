<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Relatório Vistoria {{ $vistoria->codigo }}</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
            color: #111827;
            margin: 20px;
        }
        h1 {
            margin: 0 0 8px;
            font-size: 20px;
        }
        h2 {
            font-size: 14px;
            margin: 18px 0 8px;
        }
        .muted {
            color: #4b5563;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 8px;
        }
        th, td {
            border: 1px solid #d1d5db;
            padding: 6px 8px;
            vertical-align: top;
        }
        th {
            background: #f3f4f6;
            text-align: left;
            width: 22%;
        }
        .item {
            page-break-inside: avoid;
            border: 1px solid #d1d5db;
            margin-bottom: 10px;
            padding: 10px;
        }
        .photo {
            width: 100%;
            max-height: 240px;
            object-fit: cover;
            border: 1px solid #d1d5db;
            margin-top: 8px;
        }
        .meta {
            margin-top: 8px;
            line-height: 1.5;
        }
    </style>
</head>
<body>
    <h1>Relatório de Vistoria {{ $vistoria->codigo }}</h1>
    <div class="muted">
        Condomínio: {{ $vistoria->condominio->nome ?? '-' }} ({{ $vistoria->condominio->codigo ?? '-' }})<br>
        Cidade/UF: {{ $vistoria->condominio->cidade ?? '-' }}/{{ $vistoria->condominio->uf ?? '-' }}<br>
        Emitido em: {{ now()->format('d/m/Y H:i') }}
    </div>

    <h2>Resumo</h2>
    <table>
        <tbody>
            <tr>
                <th>Tipo</th>
                <td>{{ str_replace('_', ' ', ucfirst($vistoria->tipo)) }}</td>
                <th>Status</th>
                <td>{{ str_replace('_', ' ', ucfirst($vistoria->status)) }}</td>
            </tr>
            <tr>
                <th>Risco geral</th>
                <td>{{ $vistoria->risco_geral }}%</td>
                <th>Competência</th>
                <td>{{ optional($vistoria->competencia)->format('d/m/Y') ?: '-' }}</td>
            </tr>
            <tr>
                <th>Responsável</th>
                <td>{{ $vistoria->responsavel_nome ?: '-' }}</td>
                <th>Período</th>
                <td>
                    {{ optional($vistoria->iniciada_em)->format('d/m/Y H:i') ?: '-' }}
                    até
                    {{ optional($vistoria->finalizada_em)->format('d/m/Y H:i') ?: '-' }}
                </td>
            </tr>
            <tr>
                <th>Observações</th>
                <td colspan="3">{{ $vistoria->observacoes ?: '-' }}</td>
            </tr>
        </tbody>
    </table>

    <h2>Itens inspecionados ({{ $vistoria->itens->count() }})</h2>
    @forelse ($vistoria->itens as $item)
        <section class="item">
            <strong>{{ $item->item_nome }}</strong>
            <div class="meta">
                Área: {{ $item->area->nome ?? '-' }} ({{ $item->area->codigo ?? '-' }})<br>
                Código: {{ $item->item_codigo ?: '-' }}<br>
                Categoria: {{ str_replace('_', ' ', ucfirst($item->categoria)) }}<br>
                Status: {{ str_replace('_', ' ', ucfirst($item->status)) }}<br>
                Criticidade: {{ ucfirst($item->criticidade) }}<br>
                Inspecionado em: {{ optional($item->inspecionado_em)->format('d/m/Y H:i') ?: '-' }}<br>
                Observação: {{ $item->observacao ?: '-' }}
            </div>

            @php
                $absolutePath = $item->foto_path ? public_path($item->foto_path) : null;
                $ext = $absolutePath ? strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION)) : null;
                $mime = match ($ext) {
                    'png' => 'image/png',
                    'webp' => 'image/webp',
                    default => 'image/jpeg',
                };
            @endphp

            @if ($absolutePath && file_exists($absolutePath))
                <img class="photo" src="data:{{ $mime }};base64,{{ base64_encode(file_get_contents($absolutePath)) }}" alt="Foto item {{ $item->item_nome }}">
            @endif
        </section>
    @empty
        <div class="muted">Sem itens registrados.</div>
    @endforelse
</body>
</html>
