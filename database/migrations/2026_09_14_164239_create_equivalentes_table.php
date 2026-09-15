<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equivalentes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rolamento_id')->constrained('rolamentos')->cascadeOnDelete();
            $table->string('fabricante');
            $table->string('codigo_equivalente');
            $table->text('observacao')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equivalentes');
    }
};