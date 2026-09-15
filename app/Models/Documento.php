<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    use HasFactory;

    protected $table = 'documentos';

    protected $fillable = [
        'titulo',
        'categoria_id',
        'descricao',
        'observacao',
        'fabricante_id',
        'usuario_id',
        'versao_atual_id',
    ];

    public function categoria()
    {
        return $this->belongsTo(CategoriaDocumento::class, 'categoria_id');
    }

    public function fabricante()
    {
        return $this->belongsTo(Fabricante::class, 'fabricante_id');
    }

    public function usuario()
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function modelos()
    {
        return $this->belongsToMany(Modelo::class, 'documento_modelo', 'documento_id', 'modelo_id');
    }

    public function aplicacoes()
    {
        return $this->belongsToMany(Aplicacao::class, 'documento_aplicacao', 'documento_id', 'aplicacao_id');
    }

    public function versoes()
    {
        return $this->hasMany(DocumentoVersao::class, 'documento_id');
    }

    public function versaoAtual()
    {
        return $this->belongsTo(DocumentoVersao::class, 'versao_atual_id');
    }
}