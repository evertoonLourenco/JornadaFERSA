<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fabricantes', function (Blueprint $table) {
            // Cria a coluna e a chave estrangeira (ajuste 'categorias_documento' caso a tabela tenha outro nome)
            $table->foreignId('categoria_id')->nullable()->constrained('categorias_documento')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('fabricantes', function (Blueprint $table) {
            $table->dropForeign(['categoria_id']);
            $table->dropColumn('categoria_id');
        });
    }
};
