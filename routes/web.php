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
use App\Http\Controllers\AgenteController;

// Rota raiz
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

    // Perfis & Manuais
    Route::resource('perfis', PerfilController::class);
    Route::resource('manuais', ManualController::class);

    // --- MÓDULO: DOCUMENTOS ---
    Route::get('/documentos', [DocumentoController::class, 'index'])->name('documentos.index');
    Route::get('/documentos/create', [DocumentoController::class, 'create'])->name('documentos.create');
    Route::post('/documentos', [DocumentoController::class, 'store'])->name('documentos.store');
    Route::get('/documentos/{id}/download', [DocumentoController::class, 'download'])->name('documentos.download');

    // --- MÓDULO: ESTRUTURA TÉCNICA E CADASTROS BASE (CRUDs) ---
    Route::resource('categorias', CategoriaController::class)->except(['show']);
    Route::resource('fabricantes', FabricanteController::class)->except(['show']);
    Route::resource('tipos-equipamento', TipoEquipamentoController::class)->except(['show']);
    Route::resource('modelos', ModeloController::class)->except(['show']);
    Route::resource('aplicacoes', AplicacaoController::class)->except(['show']);

    // Rotas para alternar Status (Ativo/Inativo)
    Route::patch('/categorias/{categoria}/toggle', [CategoriaController::class, 'toggleStatus'])->name('categorias.toggle');
    Route::patch('/fabricantes/{fabricante}/toggle', [FabricanteController::class, 'toggleStatus'])->name('fabricantes.toggle');
    Route::patch('/modelos/{modelo}/toggle', [ModeloController::class, 'toggleStatus'])->name('modelos.toggle');

    // --- ENDPOINTS AJAX PARA SELEÇÕES DEPENDENTES ---
    Route::get('/api/tipos-equipamento', [DocumentoController::class, 'getTiposEquipamento'])->name('api.tipos-equipamento');
    Route::get('/api/modelos', [DocumentoController::class, 'getModelos'])->name('api.modelos');
    Route::get('/api/aplicacoes', [DocumentoController::class, 'getAplicacoes'])->name('api.aplicacoes');
    Route::get('/api/fabricantes-por-categoria', [ModeloController::class, 'getFabricantesPorCategoria'])->name('api.fabricantes.por-categoria');
    
    Route::get('/categorias/{categoria}/fabricantes', [FabricanteController::class, 'porCategoria']);

    // Rotas para gestão de Aplicações
    Route::post('/aplicacoes', [AplicacaoController::class, 'store'])->name('aplicacoes.store');
    Route::patch('/aplicacoes/{aplicacao}/toggle', [AplicacaoController::class, 'toggle'])->name('aplicacoes.toggle');
    Route::delete('/aplicacoes/{aplicacao}', [AplicacaoController::class, 'destroy'])->name('aplicacoes.destroy');

    Route::post('/documentos/{id}/analisar', [DocumentoController::class, 'analisar'])->name('documentos.analisar');
    Route::post('/agente/chat', [AgenteController::class, 'chat'])->name('agente.chat');
    Route::get('/agente/documentos', [AgenteController::class, 'documentos'])->name('agente.documentos');
});