<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Usuario extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'condominio_id',
        'nome',
        'email',
        'telefone',
        'password',
        'tipo',
        'ativo',
        'ultimo_login_at',
        'force_password_change',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'ativo' => 'boolean',
            'force_password_change' => 'boolean',
            'ultimo_login_at' => 'datetime',
        ];
    }

    public function conflitosComoMoradorA(): HasMany
    {
        return $this->hasMany(ConflitoMorador::class, 'morador_a_id');
    }

    public function conflitosComoMoradorB(): HasMany
    {
        return $this->hasMany(ConflitoMorador::class, 'morador_b_id');
    }

    public function ocorrenciasComoFuncionario(): HasMany
    {
        return $this->hasMany(OcorrenciaFuncionario::class, 'funcionario_id');
    }
}
