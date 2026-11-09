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
        'fabricante_id',
        'modelo_id',
        'aplicacao_id',
        'descricao',
        'observacao',
        'usuario_id',
        'nome_arquivo',
        'caminho_arquivo',
        'tamanho_bytes',
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

    public function modelos()
    {
        return $this->belongsToMany(Modelo::class, 'documento_modelo', 'documento_id', 'modelo_id')->withTimestamps();
    }

    public function aplicacoes()
    {
        return $this->belongsToMany(Aplicacao::class, 'documento_aplicacao', 'documento_id', 'aplicacao_id')->withTimestamps();
    }

    public function versoes()
    {
        return $this->hasMany(DocumentoVersao::class, 'documento_id');
    }

    public function versaoAtual()
    {
        return $this->belongsTo(DocumentoVersao::class, 'versao_atual_id');
    }

    public function getModeloAttribute()
    {
        return $this->modelos->first();
    }

    public function getAplicacaoAttribute()
    {
        return $this->aplicacoes->first();
    }

    public function getVersaoAttribute()
    {
        return $this->versaoAtual->versao ?? '1.0';
    }

    public function getArquivoAttribute()
    {
        return $this->caminho_arquivo ?? ($this->versaoAtual->caminho_arquivo ?? '');
    }
}