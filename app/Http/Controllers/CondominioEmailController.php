<?php

namespace App\Http\Controllers;

use App\Models\Condominio;
use App\Models\CondominioEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class CondominioEmailController extends Controller
{
    public function index(Condominio $condominio): View
    {
        return view('condominios.config-emails', [
            'condominio' => $condominio,
            'emails' => CondominioEmail::query()
                ->where('condominio_id', $condominio->id)
                ->orderBy('tipo')
                ->orderBy('nome')
                ->get(),
            'tipos' => $this->tipos(),
        ]);
    }

    public function store(Request $request, Condominio $condominio): RedirectResponse
    {
        $data = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('condominio_emails', 'email')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id)),
            ],
            'tipo' => ['required', Rule::in(array_keys($this->tipos()))],
            'ativo' => ['nullable', 'boolean'],
        ]);

        CondominioEmail::query()->create([
            'condominio_id' => $condominio->id,
            'nome' => $data['nome'],
            'email' => $data['email'],
            'tipo' => $data['tipo'],
            'ativo' => (bool) ($data['ativo'] ?? true),
        ]);

        return redirect()
            ->route('condominios.context.emails.index', $condominio)
            ->with('success', 'Destinatário cadastrado com sucesso.');
    }

    public function update(Request $request, Condominio $condominio, CondominioEmail $email): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $email->condominio_id);

        $data = $request->validate([
            'nome' => ['required', 'string', 'max:120'],
            'email' => [
                'required',
                'email',
                'max:150',
                Rule::unique('condominio_emails', 'email')
                    ->where(fn ($query) => $query->where('condominio_id', $condominio->id))
                    ->ignore($email->id),
            ],
            'tipo' => ['required', Rule::in(array_keys($this->tipos()))],
            'ativo' => ['nullable', 'boolean'],
        ]);

        $email->update([
            'nome' => $data['nome'],
            'email' => $data['email'],
            'tipo' => $data['tipo'],
            'ativo' => (bool) ($data['ativo'] ?? false),
        ]);

        return redirect()
            ->route('condominios.context.emails.index', $condominio)
            ->with('success', 'Destinatário atualizado com sucesso.');
    }

    public function destroy(Condominio $condominio, CondominioEmail $email): RedirectResponse
    {
        $this->assertSameCondominio($condominio, $email->condominio_id);
        $email->delete();

        return redirect()
            ->route('condominios.context.emails.index', $condominio)
            ->with('success', 'Destinatário removido.');
    }

    private function tipos(): array
    {
        return [
            'sindico' => 'Síndico',
            'conselho' => 'Conselho',
            'adm' => 'Administradora',
            'outros' => 'Outros',
        ];
    }

    private function assertSameCondominio(Condominio $condominio, string $resourceCondominioId): void
    {
        if ((string) $condominio->id !== (string) $resourceCondominioId) {
            abort(404);
        }
    }
}
