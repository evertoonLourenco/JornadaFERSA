<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Rolamento extends Model
{
    protected $table = 'rolamentos';
    protected $fillable = ['codigo', 'descricao', 'tipo', 'diametro_interno', 'diametro_externo', 'largura', 'ativo'];

    public function equivalentes(): HasMany
    {
        return $this->hasMany(Equivalente::class, 'rolamento_id');
    }

    public function aplicacoes(): BelongsToMany
    {
        return $this->belongsToMany(Aplicacao::class, 'aplicacao_rolamentos', 'rolamento_id', 'aplicacao_id')
                    ->withPivot('quantidade', 'observacao')
                    ->withTimestamps();
    }
}