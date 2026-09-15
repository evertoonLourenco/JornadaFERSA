<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documento_versoes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->string('versao');
            $table->string('nome_arquivo');
            $table->string('caminho_arquivo');
            $table->foreignId('usuario_id')->constrained('usuarios');
            $table->timestamp('data_upload')->useCurrent();
            $table->text('observacao')->nullable();
            $table->boolean('atual')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_versoes');
    }
};