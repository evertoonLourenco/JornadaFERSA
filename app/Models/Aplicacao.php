<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Aplicacao extends Model
{
    use HasFactory;

    protected $table = 'aplicacoes';

    protected $fillable = [
        'modelo_id',
        'componente',
        'posicao',
        'lado',
        'descricao',
        'observacao',
        'ativo',
    ];

    /**
     * Relacionamento com o Modelo do Equipamento
     */
    public function modelo(): BelongsTo
    {
        return $this->belongsTo(Modelo::class, 'modelo_id');
    }

    /**
     * Relacionamento N:N com Rolamentos
     */
    public function rolamentos(): BelongsToMany
    {
        return $this->belongsToMany(Rolamento::class, 'aplicacao_rolamentos', 'aplicacao_id', 'rolamento_id')
                    ->withPivot('quantidade', 'observacao')
                    ->withTimestamps();
    }

    /**
     * Relacionamento N:N com Documentos
     */
    public function documentos(): BelongsToMany
    {
        return $this->belongsToMany(Documento::class, 'documento_aplicacao', 'aplicacao_id', 'documento_id')
                    ->withTimestamps();
    }
}