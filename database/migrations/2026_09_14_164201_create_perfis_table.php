<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabela de Perfis
        Schema::create('perfis', function (Blueprint $table) {
            $table->id();
            $table->string('nome'); // Ex: "Administrador de Vendas", "Visualizador de Usuários"
            $table->boolean('is_admin_geral')->default(false); // Apenas o Admin Geral
            $table->json('permissoes')->nullable(); // Ex: {"usuarios":"admin", "vendas":"ver", "documentos":"nenhum"}
            $table->timestamps();
        });

        // Adiciona FK de perfil na tabela de usuários
        Schema::table('usuarios', function (Blueprint $table) {
            $table->foreignId('perfil_id')->nullable()->constrained('perfis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropForeign(['perfil_id']);
            $table->dropColumn('perfil_id');
        });
        Schema::dropIfExists('perfis');
    }
};