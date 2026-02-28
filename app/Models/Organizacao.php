<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Organizacao extends Model
{
    use HasUuids, SoftDeletes;
    
    protected $table = 'organizacoes';
    
    protected $fillable = [
        'tipo', 'nome', 'documento', 'email', 'telefone',
        'modulos_ativos', 'configuracoes'
    ];
    
    protected $casts = [
        'modulos_ativos' => 'array',
        'configuracoes' => 'array',
    ];
    
    public function users() {
        return $this->belongsToMany(User::class, 'organizacao_user');
    }
}
