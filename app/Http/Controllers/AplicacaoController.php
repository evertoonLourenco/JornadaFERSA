<?php

namespace App\Http\Controllers;

use App\Models\Aplicacao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AplicacaoController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'modelo_id'  => 'required|exists:modelos,id',
            'componente' => 'required|string|max:255',
            'posicao'    => 'nullable|string|max:255',
            'lado'       => 'nullable|string|max:255',
            'descricao'  => 'nullable|string|max:255',
            'observacao' => 'nullable|string',
        ], [
            'modelo_id.required'  => 'É obrigatório selecionar um Modelo.',
            'modelo_id.exists'    => 'O modelo selecionado não é válido.',
            'componente.required' => 'O campo Componente é obrigatório.',
        ]);

        Aplicacao::create([
            'modelo_id'  => $request->modelo_id,
            'componente' => trim($request->componente),
            'posicao'    => $request->posicao,
            'lado'       => $request->lado,
            'descricao'  => $request->descricao,
            'observacao' => $request->observacao,
            'ativo'      => true,
        ]);

        return redirect()->back()->with('success', 'Aplicação criada com sucesso!');
    }

    /**
     * Alterna o status (Ativo/Inativo) da Aplicação.
     */
    public function toggle($id)
    {
        $aplicacao = Aplicacao::findOrFail($id);
        $aplicacao->ativo = !$aplicacao->ativo;
        $aplicacao->save();

        $status = $aplicacao->ativo ? 'ativada' : 'desativada';

        return redirect()->back()->with('success', "Aplicação {$status} com sucesso!");
    }

    /**
     * Exclui a Aplicação limpando todas as dependências e chaves estrangeiras.
     */
    public function destroy($id)
    {
        try {
            DB::transaction(function () use ($id) {
                // 1. Desativa temporariamente a verificação de foreign keys no SQLite
                DB::statement('PRAGMA foreign_keys = OFF;');

                // 2. Remove registros vinculados nas tabelas pivô
                DB::table('documento_aplicacao')->where('aplicacao_id', $id)->delete();
                DB::table('aplicacao_rolamentos')->where('aplicacao_id', $id)->delete();

                // 3. Desvincula o documento caso a coluna exista na tabela documentos
                try {
                    DB::table('documentos')->where('aplicacao_id', $id)->update(['aplicacao_id' => null]);
                } catch (\Throwable $e) {
                    // Ignora caso a coluna não exista ou seja NOT NULL
                }

                // 4. Remove a aplicação
                DB::table('aplicacoes')->where('id', $id)->delete();

                // 5. Reativa a verificação de foreign keys
                DB::statement('PRAGMA foreign_keys = ON;');
            });

            return redirect()->back()->with('success', 'Aplicação removida com sucesso!');

        } catch (\Throwable $e) {
            Log::error('Erro ao excluir aplicação: ' . $e->getMessage());

            return redirect()->back()->withErrors([
                'msg' => 'Erro ao excluir a aplicação: ' . $e->getMessage()
            ]);
        }
    }
}