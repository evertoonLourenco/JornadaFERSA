<?php

namespace App\Http\Controllers;

use App\Models\TipoEquipamento;
use Illuminate\Http\Request;

class TipoEquipamentoController extends Controller
{
    public function index()
    {
        $tipos = TipoEquipamento::withCount('modelos')->orderBy('nome')->paginate(15);
        return view('cadastros.tipos_equipamento.index', compact('tipos'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:tipos_equipamento,nome',
        ], [
            'nome.required' => 'Informe o tipo de equipamento.',
            'nome.unique' => 'Este tipo de equipamento já está cadastrado.'
        ]);

        TipoEquipamento::create(['nome' => $request->nome]);

        return back()->with('success', 'Tipo de equipamento cadastrado com sucesso!');
    }

    public function update(Request $request, TipoEquipamento $tipos_equipamento)
    {
        $request->validate([
            'nome' => 'required|string|max:100|unique:tipos_equipamento,nome,' . $tipos_equipamento->id,
        ]);

        $tipos_equipamento->update(['nome' => $request->nome]);

        return back()->with('success', 'Tipo de equipamento atualizado com sucesso!');
    }

    public function destroy(TipoEquipamento $tipos_equipamento)
    {
        if ($tipos_equipamento->modelos()->exists()) {
            return back()->withErrors(['erro' => 'Não é possível excluir este tipo pois existem modelos vinculados.']);
        }

        $tipos_equipamento->delete();
        return back()->with('success', 'Tipo de equipamento removido!');
    }
}