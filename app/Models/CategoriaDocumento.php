<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriaDocumento extends Model
{
    use HasFactory;

    protected $table = 'categorias_documento';
    protected $fillable = ['nome'];

    public function fabricantes()
    {
        return $this->hasMany(Fabricante::class, 'categoria_id');
    }
}