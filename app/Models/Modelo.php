<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $table = 'modelos';

    protected $fillable = [
        'fabricante_id',
        'tipo_id',
        'modelo',
        'ano_inicio',
        'ano_fim',
        'descricao',
        'ativo',
    ];

    public function fabricante()
    {
        return $this->belongsTo(Fabricante::class, 'fabricante_id');
    }

    public function tipoEquipamento()
    {
        return $this->belongsTo(TipoEquipamento::class, 'tipo_id');
    }

    public function aplicacoes()
    {
        return $this->hasMany(Aplicacao::class, 'modelo_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'modelo_id');
    }
}