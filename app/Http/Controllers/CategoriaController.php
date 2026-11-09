<?php

namespace App\Http\Controllers;

use App\Models\CategoriaDocumento;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Cadastra uma nova Categoria.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nome' => 'required|string|max:255|unique:categorias_documento,nome',
            'descricao' => 'nullable|string|max:1000',
        ], [
            'nome.required' => 'O nome da categoria é obrigatório.',
            'nome.unique' => 'Já existe uma categoria cadastrada com este nome.',
        ]);

        CategoriaDocumento::create([
            'nome' => $request->nome,
            'descricao' => $request->descricao,
            'ativo' => true,
        ]);

        return redirect()->back()->with('success', 'Categoria criada com sucesso!');
    }

    /**
     * Alterna o status (Ativo/Inativo) da Categoria (chamado pelas rotas como toggleStatus).
     */
    public function toggleStatus(CategoriaDocumento $categoria)
    {
        $categoria->ativo = !$categoria->ativo;
        $categoria->save();

        $status = $categoria->ativo ? 'ativada' : 'desativada';

        return redirect()->back()->with('success', "Categoria {$status} com sucesso!");
    }

    /**
     * Alias para compatibilidade caso alguma rota chame toggle().
     */
    public function toggle(CategoriaDocumento $categoria)
    {
        return $this->toggleStatus($categoria);
    }

    /**
     * Exclui a Categoria se não houver vínculos existentes.
     */
    public function destroy(CategoriaDocumento $categoria)
    {
        $temFabricantes = $categoria->fabricantes()->exists();
        $temDocumentos  = $categoria->documentos()->exists();

        if ($temFabricantes || $temDocumentos) {
            $motivo = [];
            if ($temFabricantes) $motivo[] = 'fabricantes';
            if ($temDocumentos)  $motivo[] = 'documentos';

            return redirect()->back()->withErrors([
                'msg' => 'Não é possível excluir a categoria "' . $categoria->nome . '" pois ela possui ' . implode(' e ', $motivo) . ' vinculados.'
            ]);
        }

        $categoria->delete();

        return redirect()->back()->with('success', 'Categoria excluída com sucesso!');
    }
}