@csrf

<div class="form-grid">
    <div class="field">
        <label for="codigo">Código</label>
        <input id="codigo" name="codigo" value="{{ old('codigo', $condominio->codigo) }}" required maxlength="20">
    </div>
    <div class="field">
        <label for="nome">Nome</label>
        <input id="nome" name="nome" value="{{ old('nome', $condominio->nome) }}" required maxlength="150">
    </div>
    <div class="field">
        <label for="cnpj">CNPJ</label>
        <input id="cnpj" name="cnpj" value="{{ old('cnpj', $condominio->cnpj) }}" maxlength="14">
    </div>
    <div class="field">
        <label for="cep">CEP</label>
        <input id="cep" name="cep" value="{{ old('cep', $condominio->cep) }}" maxlength="8" inputmode="numeric" pattern="[0-9]*" placeholder="Somente números">
        <span id="cep-feedback" class="muted"></span>
    </div>
    <div class="field">
        <label for="logradouro">Logradouro</label>
        <input id="logradouro" name="logradouro" value="{{ old('logradouro', $condominio->logradouro) }}" maxlength="150">
    </div>
    <div class="field">
        <label for="numero">Número</label>
        <input id="numero" name="numero" value="{{ old('numero', $condominio->numero) }}" maxlength="20">
    </div>
    <div class="field">
        <label for="bairro">Bairro</label>
        <input id="bairro" name="bairro" value="{{ old('bairro', $condominio->bairro) }}" maxlength="100">
    </div>
    <div class="field">
        <label for="cidade">Cidade</label>
        <input id="cidade" name="cidade" value="{{ old('cidade', $condominio->cidade) }}" maxlength="100">
    </div>
    <div class="field">
        <label for="uf">UF</label>
        <input id="uf" name="uf" value="{{ old('uf', $condominio->uf) }}" maxlength="2">
    </div>
    <div class="field">
        <label for="timezone">Timezone</label>
        <input id="timezone" name="timezone" value="{{ old('timezone', $condominio->timezone ?? 'America/Sao_Paulo') }}" required maxlength="60">
    </div>
    <div class="field">
        <label for="status">Status</label>
        <select id="status" name="status" required>
            @php($status = old('status', $condominio->status ?? 'ativo'))
            <option value="ativo" @selected($status === 'ativo')>Ativo</option>
            <option value="inativo" @selected($status === 'inativo')>Inativo</option>
            <option value="suspenso" @selected($status === 'suspenso')>Suspenso</option>
        </select>
    </div>
</div>

<div class="form-actions">
    <a class="btn" href="{{ route('condominios.index') }}">Cancelar</a>
    <button class="btn-primary" type="submit">{{ $buttonLabel }}</button>
</div>

@once
    <script>
        (function () {
            const cepInput = document.getElementById('cep');
            const logradouroInput = document.getElementById('logradouro');
            const bairroInput = document.getElementById('bairro');
            const cidadeInput = document.getElementById('cidade');
            const ufInput = document.getElementById('uf');
            const feedback = document.getElementById('cep-feedback');

            if (!cepInput) {
                return;
            }

            cepInput.addEventListener('input', function () {
                this.value = this.value.replace(/\D/g, '').slice(0, 8);
            });

            async function buscarCep() {
                const cep = cepInput.value.replace(/\D/g, '');
                if (cep.length !== 8) {
                    return;
                }

                if (feedback) {
                    feedback.textContent = 'Buscando CEP...';
                }

                try {
                    const response = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                    if (!response.ok) {
                        throw new Error('Falha na consulta do CEP.');
                    }

                    const data = await response.json();
                    if (data.erro) {
                        if (feedback) {
                            feedback.textContent = 'CEP não encontrado.';
                        }
                        return;
                    }

                    if (logradouroInput) {
                        logradouroInput.value = data.logradouro || '';
                    }
                    if (bairroInput) {
                        bairroInput.value = data.bairro || '';
                    }
                    if (cidadeInput) {
                        cidadeInput.value = data.localidade || '';
                    }
                    if (ufInput) {
                        ufInput.value = data.uf || '';
                    }

                    if (feedback) {
                        feedback.textContent = 'Endereço preenchido pelo CEP (número é manual).';
                    }
                } catch (error) {
                    if (feedback) {
                        feedback.textContent = 'Não foi possível consultar o CEP agora.';
                    }
                }
            }

            cepInput.addEventListener('blur', buscarCep);
        })();
    </script>
@endonce
