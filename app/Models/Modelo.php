<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modelo extends Model
{
    use HasFactory;

    protected $table = 'modelos';

    protected $fillable = [
        'modelo',        // Se no seu banco a coluna for 'modelo'
        'nome',          // Se a coluna for 'nome' (mantenha a que existir no seu banco)
        'fabricante_id',
        'tipo_id',
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
}