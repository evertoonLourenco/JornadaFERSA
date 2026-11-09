<?php

namespace App\Http\Controllers;

use App\Models\CategoriaDocumento;
use App\Models\Fabricante;
use App\Models\Modelo;
use App\Models\Aplicacao;
use App\Models\Documento;
use App\Models\DocumentoVersao;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Services\GeminiService;

class DocumentoController extends Controller
{
    public function index()
    {
        $categorias = CategoriaDocumento::orderBy('id', 'desc')->get();
        $categoriasAtivas = CategoriaDocumento::where('ativo', true)->orderBy('nome', 'asc')->get();

        $fabricantes = Fabricante::with('categoria')->orderBy('id', 'desc')->get();
        $fabricantesAtivos = Fabricante::where('ativo', true)
            ->whereHas('categoria', function ($query) {
                $query->where('ativo', true);
            })
            ->orderBy('nome', 'asc')
            ->get();

        $modelos = Modelo::with('fabricante')->orderBy('modelo', 'asc')->get();
        $aplicacoes = Aplicacao::orderBy('componente', 'asc')->get();

        $documentos = Documento::with(['categoria', 'fabricante', 'modelos', 'aplicacoes', 'versaoAtual'])
            ->latest()
            ->get();

        return view('documentos.index', compact(
            'categorias', 
            'categoriasAtivas',
            'fabricantes', 
            'fabricantesAtivos',
            'modelos', 
            'aplicacoes',
            'documentos'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'titulo'        => 'required|string|max:255',
            'categoria_id'  => ['required', Rule::exists(CategoriaDocumento::class, 'id')],
            'fabricante_id' => ['nullable', Rule::exists(Fabricante::class, 'id')],
            'modelo_id'     => ['nullable', Rule::exists(Modelo::class, 'id')],
            'aplicacao_id'  => ['nullable', Rule::exists(Aplicacao::class, 'id')],
            'versao'        => 'required|string|max:50',
            'data_documento'=> 'nullable|date',
            'arquivo'       => 'required|file|max:20480',
            'descricao'     => 'nullable|string',
            'observacao'    => 'nullable|string',
        ], [
            'titulo.required'       => 'Informe o título do documento.',
            'categoria_id.required' => 'Selecione uma categoria.',
            'categoria_id.exists'   => 'A categoria selecionada é inválida.',
            'versao.required'       => 'Informe a versão do documento.',
            'arquivo.required'      => 'Selecione um ficheiro para envio.',
            'arquivo.file'          => 'O ficheiro enviado é inválido.',
        ]);

        $file = $request->file('arquivo');
        $filePath = null;

        DB::beginTransaction();

        try {
            // 1. Armazenamento físico do ficheiro
            $subFolder = 'documents/' . date('Y/m');
            $filePath = $file->store($subFolder, 'local');

            if (!$filePath) {
                throw new \Exception('Não foi possível armazenar o ficheiro no servidor.');
            }

            // 2. Mapeamento de campos para a tabela 'documentos'
            $documentoCampos = [
                'titulo'          => $request->titulo,
                'categoria_id'    => $request->categoria_id,
                'fabricante_id'   => $request->fabricante_id,
                'modelo_id'       => $request->modelo_id,
                'aplicacao_id'    => $request->aplicacao_id,
                'descricao'       => $request->descricao,
                'observacao'      => $request->observacao,
                'usuario_id'      => auth()->id(),
                'nome_arquivo'    => $file->getClientOriginalName(),
                'caminho_arquivo' => $filePath,
                'tamanho_bytes'   => $file->getSize(),
            ];

            // Filtra dinamicamente APENAS as colunas existentes no banco SQLite
            $colunasDocumentos = Schema::getColumnListing('documentos');
            $documentoData = array_intersect_key($documentoCampos, array_flip($colunasDocumentos));

            $documento = Documento::create($documentoData);

            // 3. Associação pivô se a coluna direta não existir no schema
            if ($request->filled('modelo_id') && !in_array('modelo_id', $colunasDocumentos) && method_exists($documento, 'modelos')) {
                try { $documento->modelos()->sync([$request->modelo_id]); } catch (\Throwable $e) {}
            }
            if ($request->filled('aplicacao_id') && !in_array('aplicacao_id', $colunasDocumentos) && method_exists($documento, 'aplicacoes')) {
                try { $documento->aplicacoes()->sync([$request->aplicacao_id]); } catch (\Throwable $e) {}
            }

            // 4. Mapeamento de campos para a tabela 'documento_versoes'
            $versaoCampos = [
                'documento_id'    => $documento->id,
                'versao'          => $request->versao ?? '1.0',
                'data_documento'  => $request->data_documento ?? date('Y-m-d'),
                'nome_arquivo'    => $file->getClientOriginalName(),
                'caminho_arquivo' => $filePath,
                'tamanho_bytes'   => $file->getSize(),
                'usuario_id'      => auth()->id(),
            ];

            // Filtra dinamicamente APENAS as colunas existentes na tabela 'documento_versoes'
            $colunasVersoes = Schema::getColumnListing('documento_versoes');
            $versaoData = array_intersect_key($versaoCampos, array_flip($colunasVersoes));

            $versao = DocumentoVersao::create($versaoData);

            // 5. Aponta para a versão atual caso a coluna exista
            if (in_array('versao_atual_id', $colunasDocumentos)) {
                $documento->update(['versao_atual_id' => $versao->id]);
            }

            DB::commit();

            return redirect()->route('documentos.index')
                ->with('success', 'Documento cadastrado com sucesso.');

        } catch (\Throwable $e) {
            DB::rollBack();

            if ($filePath && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            }

            Log::error('Erro ao cadastrar documento: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['erro_geral' => 'Falha ao cadastrar o documento: ' . $e->getMessage()]);
        }
    }

    public function getModelos(Request $request)
    {
        $fabricanteId = $request->query('fabricante_id');
        
        if (!$fabricanteId) {
            return response()->json([]);
        }

        $modelos = Modelo::where('fabricante_id', $fabricanteId)
            ->where('ativo', true)
            ->orderBy('modelo', 'asc')
            ->get();

        return response()->json($modelos);
    }

    public function getAplicacoes(Request $request)
    {
        $modeloId = $request->query('modelo_id');

        if (!$modeloId) {
            return response()->json([]);
        }

        $aplicacoes = Aplicacao::where('modelo_id', $modeloId)
            ->orderBy('componente', 'asc')
            ->get(['id', 'componente', 'descricao']);

        return response()->json($aplicacoes);
    }
    public function download($id)
    {
        $documento = Documento::with('versaoAtual')->findOrFail($id);

        // Busca o caminho do ficheiro (no documento ou na versão atual)
        $caminho = $documento->caminho_arquivo 
            ?? ($documento->versaoAtual->caminho_arquivo ?? null);

        // Define o nome de exibição no download
        $nomeOriginal = $documento->nome_arquivo 
            ?? ($documento->versaoAtual->nome_arquivo ?? ($documento->titulo . '.pdf'));

        if (!$caminho) {
            return back()->withErrors(['erro_geral' => 'Caminho do ficheiro não registado no sistema.']);
        }

        // 1. Tenta descarregar via driver 'local' do Storage
        if (Storage::disk('local')->exists($caminho)) {
            return Storage::disk('local')->download($caminho, $nomeOriginal);
        }

        // 2. Fallback: verificação direta no caminho físico storage/app/
        $caminhoAbsoluto = storage_path('app/' . $caminho);
        if (file_exists($caminhoAbsoluto)) {
            return response()->download($caminhoAbsoluto, $nomeOriginal);
        }

        // 3. Fallback Laravel 11+: verificação no caminho storage/app/private/
        $caminhoPrivate = storage_path('app/private/' . $caminho);
        if (file_exists($caminhoPrivate)) {
            return response()->download($caminhoPrivate, $nomeOriginal);
        }

        return back()->withErrors(['erro_geral' => 'O ficheiro físico não foi encontrado no servidor.']);
    }
    public function analisar(Request $request, $id, GeminiService $geminiService)
    {
        $request->validate([
            'pergunta' => 'required|string|max:1000',
        ], [
            'pergunta.required' => 'Escreva uma pergunta ou instrução para a IA.',
        ]);

        $documento = Documento::with('versaoAtual')->findOrFail($id);

        $caminho = $documento->caminho_arquivo 
            ?? ($documento->versaoAtual->caminho_arquivo ?? null);

        if (!$caminho) {
            return response()->json([
                'success' => false, 
                'error' => 'Caminho do arquivo não encontrado no sistema.'
            ], 404);
        }

        try {
            $resposta = $geminiService->analisarDocumento($caminho, $request->pergunta);

            return response()->json([
                'success' => true,
                'titulo'   => $documento->titulo,
                'resposta' => $resposta
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}