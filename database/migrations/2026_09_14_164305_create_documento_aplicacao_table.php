<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('documento_aplicacao', function (Blueprint $table) {
            $table->id();
            $table->foreignId('documento_id')->constrained('documentos')->cascadeOnDelete();
            $table->foreignId('aplicacao_id')->constrained('aplicacoes')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['documento_id', 'aplicacao_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('documento_aplicacao');
    }
};