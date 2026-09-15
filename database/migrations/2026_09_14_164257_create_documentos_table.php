<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categoria_id')->constrained('categorias_documento');
            $table->string('titulo');
            $table->text('descricao')->nullable();
            $table->string('nome_arquivo');
            $table->string('caminho_arquivo');
            $table->string('versao_atual')->default('1.0');
            $table->date('data_documento')->nullable();
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->boolean('ativo')->default(true);
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documentos');
    }
};