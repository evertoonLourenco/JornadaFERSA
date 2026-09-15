<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PerfilController;
use App\Http\Controllers\ManualController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\FabricanteController;
use App\Http\Controllers\TipoEquipamentoController;
use App\Http\Controllers\ModeloController;
use App\Http\Controllers\AplicacaoController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('login');
});

// Rotas de Autenticação
Route::get('/login', [AuthController::class, 'show'])->name('login');
Route::post('/login', [AuthController::class, 'perform'])->name('login.perform');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rotas Protegidas
Route::middleware('auth')->group(function () {
    
    Route::get('/dashboard', function () {
        return view('index');
    })->name('dashboard');

    // Usuários
    Route::get('/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{usuario}', [UsuarioController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->name('usuarios.destroy');

    // Perfis
    Route::resource('perfis', PerfilController::class);

    // Manuais
    Route::resource('manuais', ManualController::class);

    // --- MÓDULO: DOCUMENTOS ---
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('/documentos/create', [DocumentoController::class, 'create'])->name('documentos.create');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');

    // --- MÓDULO: ESTRUTURA TÉCNICA E CADASTROS BASE ---
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('fabricantes', FabricanteController::class)->except(['show']);
    Route::resource('tipos-equipamento', TipoEquipamentoController::class)->except(['show']);
    Route::resource('modelos', ModeloController::class)->except(['show']);
    Route::resource('aplicacoes', AplicacaoController::class)->except(['show']);

    // Endpoints para seleções dependentes via AJAX
    Route::get('/api/tipos-equipamento', [DocumentoController::class, 'getTiposEquipamento'])->name('api.tipos-equipamento');
    Route::get('/api/modelos', [DocumentoController::class, 'getModelos'])->name('api.modelos');
    Route::get('/api/aplicacoes', [DocumentoController::class, 'getAplicacoes'])->name('api.aplicacoes');

});