<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->enum('tipo', ['cliente', 'funcionario'])->default('cliente')->after('email');
            $table->string('matricula')->nullable()->after('tipo');
            $table->boolean('provisorio')->default(false)->after('ativo');
            $table->date('data_expiracao')->nullable()->after('provisorio');
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['tipo', 'matricula', 'provisorio', 'data_expiracao']);
        });
    }
};