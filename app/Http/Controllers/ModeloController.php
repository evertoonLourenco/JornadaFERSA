<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\Fabricante;
use App\Models\TipoEquipamento;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::with(['fabricante', 'tipoEquipamento'])
            ->withCount('aplicacoes')
            ->latest()
            ->paginate(15);

        $fabricantes = Fabricante::orderBy('nome')->get();
        $tiposEquipamento = TipoEquipamento::orderBy('nome')->get();

        return view('cadastros.modelos.index', compact('modelos', 'fabricantes', 'tiposEquipamento'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:100',
            'fabricante_id' => 'required|exists:fabricantes,id',
            'tipo_equipamento_id' => 'required|exists:tipos_equipamento,id',
        ], [
            'nome.required' => 'Informe o nome do modelo.',
            'fabricante_id.required' => 'Selecione o fabricante.',
            'tipo_equipamento_id.required' => 'Selecione o tipo de equipamento.',
        ]);

        Modelo::create([
            'modelo' => $request->nome,        // Mapeia a entrada 'nome' para a coluna 'modelo'
            'nome'   => $request->nome,        // Se a coluna for 'nome', use esta linha
            'fabricante_id' => $request->fabricante_id,
            'tipo_id' => $request->tipo_equipamento_id,
        ]);

        return back()->with('success', 'Modelo cadastrado com sucesso!');
    }

    public function destroy(Modelo $modelo)
    {
        if ($modelo->aplicacoes()->exists()) {
            return back()->withErrors(['erro' => 'Não é possível excluir este modelo pois ele possui aplicações cadastradas.']);
        }

        $modelo->delete();
        return back()->with('success', 'Modelo removido com sucesso!');
    }
}