<?php

namespace App\Http\Controllers;

use App\Models\Aplicacao;
use App\Models\Fabricante;
use App\Models\Modelo;
use Illuminate\Http\Request;

class AplicacaoController extends Controller
{
    public function index()
    {
        $aplicacoes = Aplicacao::with(['modelo.fabricante', 'modelo.tipoEquipamento'])
            ->latest()
            ->paginate(15);

        $fabricantes = Fabricante::orderBy('nome')->get();

        return view('cadastros.aplicacoes.index', compact('aplicacoes', 'fabricantes'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'descricao' => 'required|string|max:255',
            'modelo_id' => 'required|exists:modelos,id',
        ], [
            'descricao.required' => 'Informe a descrição da aplicação.',
            'modelo_id.required' => 'Selecione o modelo do equipamento.',
        ]);

        Aplicacao::create([
            'descricao'  => $request->descricao,
            'componente' => $request->descricao, // Salva o valor no campo NOT NULL do banco
            'modelo_id'  => $request->modelo_id,
        ]);

        return back()->with('success', 'Aplicação cadastrada com sucesso!');
    }

    public function destroy(Aplicacao $aplicacoe)
    {
        $aplicacoe->delete();
        return back()->with('success', 'Aplicação removida com sucesso!');
    }
}