<?php

namespace App\Http\Controllers;

use App\Models\Fabricante;
use Illuminate\Http\Request;

class FabricanteController extends Controller
{
    /**
     * Cadastra um novo Fabricante.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:fabricantes,nome',
            'categoria_id' => 'required|exists:categorias_documento,id',
        ], [
            'nome.required' => 'O nome do fabricante é obrigatório.',
            'nome.unique' => 'Já existe um fabricante cadastrado com esse nome.',
            'categoria_id.required' => 'Selecione uma categoria válida para o fabricante.',
            'categoria_id.exists' => 'A categoria selecionada não existe.',
        ]);

        Fabricante::create([
            'nome' => $request->nome,
            'categoria_id' => $request->categoria_id,
            'ativo' => true,
        ]);

        return redirect()->back()->with('success', 'Fabricante cadastrado com sucesso!');
    }

    /**
     * Alterna o status (Ativo/Inativo) do Fabricante (chamado pelas rotas como toggleStatus).
     */
    public function toggleStatus(Fabricante $fabricante)
    {
        $fabricante->ativo = !$fabricante->ativo;
        $fabricante->save();

        $status = $fabricante->ativo ? 'ativado' : 'desativado';

        return redirect()->back()->with('success', "Fabricante {$status} com sucesso!");
    }

    /**
     * Alias para compatibilidade com rotas que chamam toggle().
     */
    public function toggle(Fabricante $fabricante)
    {
        return $this->toggleStatus($fabricante);
    }

    /**
     * Exclui o Fabricante se não houver vínculos existentes.
     */
    public function destroy(Fabricante $fabricante)
    {
        // Verifica se existem modelos vinculados a este fabricante
        $temModelos = $fabricante->modelos()->exists();

        if ($temModelos) {
            return redirect()->back()->withErrors([
                'msg' => 'Não é possível excluir o fabricante "' . $fabricante->nome . '" pois ele possui modelos vinculados a ele.'
            ]);
        }

        $fabricante->delete();

        return redirect()->back()->with('success', 'Fabricante excluído com sucesso!');
    }
}