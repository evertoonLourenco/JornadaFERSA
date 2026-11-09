<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentoVersao extends Model
{
    use HasFactory;

    protected $table = 'documento_versoes';

    protected $fillable = [
        'documento_id',
        'versao',
        'data_documento',
        'nome_arquivo',
        'caminho_arquivo',
        'tamanho_bytes',
        'usuario_id',
    ];

    public function documento()
    {
        return $this->belongsTo(Documento::class, 'documento_id');
    }
}