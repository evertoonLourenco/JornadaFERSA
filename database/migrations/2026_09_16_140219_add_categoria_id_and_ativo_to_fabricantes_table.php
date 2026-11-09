<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('aplicacoes', 'modelo_id')) {
            Schema::table('aplicacoes', function (Blueprint $table) {
                $table->foreignId('modelo_id')->nullable()->constrained('modelos');
            });
        }
    }

    public function down(): void
    {
        Schema::table('fabricantes', function (Blueprint $table) {
            if (Schema::hasColumn('fabricantes', 'categoria_id')) {
                $table->dropColumn('categoria_id');
            }
            if (Schema::hasColumn('fabricantes', 'ativo')) {
                $table->dropColumn('ativo');
            }
        });
    }
};