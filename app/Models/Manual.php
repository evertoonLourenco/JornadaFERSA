<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Manual extends Model
{
    use HasFactory;

    protected $fillable = [
        'titulo',
        'categoria',
        'perfil_id',
        'descricao',
        'arquivo_path',
        'tamanho_bytes',
    ];

    public function perfil()
    {
        return $this->belongsTo(Perfil::class);
    }
}