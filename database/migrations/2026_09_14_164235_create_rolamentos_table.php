<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('rolamentos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique();
            $table->text('descricao')->nullable();
            $table->string('tipo')->nullable();
            $table->decimal('diametro_interno', 8, 3)->nullable();
            $table->decimal('diametro_externo', 8, 3)->nullable();
            $table->decimal('largura', 8, 3)->nullable();
            $table->boolean('ativo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rolamentos');
    }
};