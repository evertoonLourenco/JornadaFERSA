<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AgenteController extends Controller
{
    public function chat(Request $request, GeminiService $geminiService)
    {
        // Evita estouro de limite de tempo de execução do PHP (30s)
        set_time_limit(120);

        try {
            $validated = $request->validate([
                'pergunta'     => 'required|string|max:1000',
                'documento_id' => 'nullable|integer|exists:documentos,id',
            ]);

            $caminho = null;
            if (!empty($validated['documento_id'])) {
                $doc = Documento::with('versaoAtual')->findOrFail($validated['documento_id']);

                if (method_exists($request->user(), 'can') && !$request->user()->can('view', $doc)) {
                    return response()->json([
                        'success' => false,
                        'error'   => 'Você não tem permissão para acessar este documento.'
                    ], 403);
                }

                $caminho = $doc->caminho_arquivo 
                    ?? ($doc->versaoAtual->caminho_arquivo ?? null);
            }

            $resposta = $geminiService->perguntar($caminho, $validated['pergunta']);

            return response()->json([
                'success'  => true,
                'resposta' => $resposta,
            ], 200);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'error'   => implode(' ', $e->validator->errors()->all()),
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Erro no AgenteController: ' . $e->getMessage(), [
                'exception' => $e->getTraceAsString(),
                'user_id'   => Auth::id(),
            ]);

            $debug = config('app.debug', false);
            $mensagem = $debug 
                ? $e->getMessage() 
                : 'Ocorreu um erro interno ao processar sua solicitação com o agente virtual.';

            return response()->json([
                'success' => false,
                'error'   => $mensagem,
            ], 500);
        }
    }

    public function documentos()
    {
        try {
            $docs = Documento::select('id', 'titulo')->orderBy('titulo', 'asc')->get();
            return response()->json($docs, 200);
        } catch (\Throwable $e) {
            Log::error('Erro ao listar documentos no AgenteController: ' . $e->getMessage());
            return response()->json([], 500);
        }
    }
}