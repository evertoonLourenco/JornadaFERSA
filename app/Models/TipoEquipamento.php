<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEquipamento extends Model
{
    protected $table = 'tipos_equipamento';
    protected $fillable = ['nome', 'descricao', 'ativo'];

    public function modelos(): HasMany
    {
        return $this->hasMany(Modelo::class, 'tipo_id');
    }
}