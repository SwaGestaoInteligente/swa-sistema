<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Condominio extends Model
{
    use HasUuids;
    use SoftDeletes;

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = [
        'codigo',
        'nome',
        'cnpj',
        'cep',
        'logradouro',
        'numero',
        'bairro',
        'cidade',
        'uf',
        'timezone',
        'status',
    ];

    public function blocos(): HasMany
    {
        return $this->hasMany(Bloco::class);
    }

    public function pavimentos(): HasMany
    {
        return $this->hasMany(Pavimento::class);
    }

    public function areas(): HasMany
    {
        return $this->hasMany(Area::class);
    }

    public function unidades(): HasMany
    {
        return $this->hasMany(Unidade::class);
    }

    public function vistorias(): HasMany
    {
        return $this->hasMany(Vistoria::class);
    }

    public function conflitosMoradores(): HasMany
    {
        return $this->hasMany(ConflitoMorador::class);
    }

    public function ocorrenciasFuncionarios(): HasMany
    {
        return $this->hasMany(OcorrenciaFuncionario::class);
    }

    public function usuarios(): HasMany
    {
        return $this->hasMany(Usuario::class);
    }

    public function templates(): HasMany
    {
        return $this->hasMany(ChecklistTemplate::class);
    }

    public function emails(): HasMany
    {
        return $this->hasMany(CondominioEmail::class);
    }

    public function relatorios(): HasMany
    {
        return $this->hasMany(Relatorio::class);
    }

    public function backups(): HasMany
    {
        return $this->hasMany(Backup::class);
    }

    public function usuariosSistema(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'condominio_user')
            ->withPivot('role')
            ->withTimestamps();
    }
}
