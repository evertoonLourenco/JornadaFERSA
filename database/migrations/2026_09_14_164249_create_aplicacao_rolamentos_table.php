<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('aplicacao_rolamentos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('aplicacao_id')->constrained('aplicacoes')->cascadeOnDelete();
            $table->foreignId('rolamento_id')->constrained('rolamentos')->cascadeOnDelete();
            $table->integer('quantidade')->default(1);
            $table->text('observacao')->nullable();
            $table->timestamps();

            $table->unique(['aplicacao_id', 'rolamento_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('aplicacao_rolamentos');
    }
};