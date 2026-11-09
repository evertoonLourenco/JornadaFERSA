<?php

namespace App\Http\Controllers;

use App\Models\Modelo;
use App\Models\Fabricante;
use App\Models\CategoriaDocumento;
use Illuminate\Http\Request;

class ModeloController extends Controller
{
    public function index()
    {
        $modelos = Modelo::with(['fabricante.categoria'])
            ->withCount('aplicacoes')
            ->latest()
            ->paginate(15);

        // Busca categorias ativas trazendo junto apenas os fabricantes ativos
        $categorias = CategoriaDocumento::where('ativo', true)
            ->with(['fabricantes' => function ($query) {
                $query->where('ativo', true)->orderBy('nome', 'asc');
            }])
            ->orderBy('nome', 'asc')
            ->get();

        return view('cadastros.modelos.index', compact('modelos', 'categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id'  => 'required',
            'fabricante_id' => 'required|exists:fabricantes,id',
            'modelo'        => 'required|string|max:255',
            'ano_inicio'    => 'nullable|integer|digits:4',
            'ano_fim'       => 'nullable|integer|digits:4|gte:ano_inicio',
            'descricao'     => 'nullable|string',
        ], [
            'categoria_id.required'  => 'Selecione a categoria.',
            'fabricante_id.required' => 'Selecione o fabricante.',
            'modelo.required'        => 'Informe o nome do modelo.',
            'ano_fim.gte'            => 'O ano final não pode ser menor que o ano inicial.',
        ]);

        Modelo::create([
            'fabricante_id' => $request->fabricante_id,
            'modelo'        => $request->modelo,
            'ano_inicio'    => $request->ano_inicio,
            'ano_fim'       => $request->ano_fim,
            'descricao'     => $request->descricao,
            'ativo'         => true,
        ]);

        return back()->with('success', 'Modelo cadastrado com sucesso!');
    }

    /**
     * Alterna o status (Ativo/Inativo) do Modelo.
     */
    public function toggleStatus(Modelo $modelo)
    {
        $modelo->ativo = !$modelo->ativo;
        $modelo->save();

        $status = $modelo->ativo ? 'ativado' : 'desativado';

        return back()->with('success', "Modelo {$status} com sucesso!");
    }

    /**
     * Alias para compatibilidade de rota.
     */
    public function toggle(Modelo $modelo)
    {
        return $this->toggleStatus($modelo);
    }

    /**
     * Exclui o Modelo se não houver registros vinculados.
     */
    public function destroy(Modelo $modelo)
    {
        $temAplicacoes = $modelo->aplicacoes()->exists();
        
        $temDocumentos = false;
        try {
            $temDocumentos = $modelo->documentos()->exists();
        } catch (\Throwable $e) {
            $temDocumentos = false;
        }

        if ($temAplicacoes || $temDocumentos) {
            $motivo = [];
            if ($temAplicacoes) $motivo[] = 'aplicações';
            if ($temDocumentos) $motivo[] = 'documentos';

            return back()->withErrors([
                'msg' => 'Não é possível excluir o modelo "' . $modelo->modelo . '" pois ele possui ' . implode(' e ', $motivo) . ' vinculados.'
            ]);
        }

        $modelo->delete();

        return back()->with('success', 'Modelo removido com sucesso!');
    }

    /**
     * Endpoint para buscar Fabricantes filtrados pela Categoria via AJAX.
     */
    public function getFabricantesPorCategoria(Request $request)
    {
        $categoriaId = $request->query('categoria_id');

        if (!$categoriaId) {
            return response()->json([]);
        }

        $fabricantes = Fabricante::where('categoria_id', $categoriaId)
            ->where('ativo', true)
            ->orderBy('nome', 'asc')
            ->get(['id', 'nome']);

        return response()->json($fabricantes);
    }
}