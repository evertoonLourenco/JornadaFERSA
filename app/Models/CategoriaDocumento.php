<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaDocumento extends Model
{
    use HasFactory;

    // Define o nome exato da tabela na base de dados
    protected $table = 'categorias_documento';

    protected $fillable = [
        'nome',
        'descricao',
        'ativo',
    ];

    public function fabricantes()
    {
        return $this->hasMany(Fabricante::class, 'categoria_id');
    }

    public function documentos()
    {
        return $this->hasMany(Documento::class, 'categoria_id');
    }
}