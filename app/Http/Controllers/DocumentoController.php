<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreDocumentoRequest;
use App\Models\CategoriaDocumento;
use App\Models\Fabricante;
use App\Models\TipoEquipamento;
use App\Models\Modelo;
use App\Models\Aplicacao;
use App\Models\Documento;
use App\Models\DocumentoVersao;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class DocumentoController extends Controller
{
    public function index()
    {
        // Busca os dados do banco para preencher os selects dos modais e filtros
        $categorias = CategoriaDocumento::orderBy('nome')->get();
        $fabricantes = Fabricante::with('categoria')->orderBy('nome')->get();
        $modelos = Modelo::with('fabricante')->orderBy('nome')->get();
        $aplicacoes = Aplicacao::orderBy('nome')->get();

        return view('documentos.index', compact(
            'categorias', 
            'fabricantes', 
            'modelos', 
            'aplicacoes'
        ));
    }

    public function create()
    {
        $categorias = CategoriaDocumento::orderBy('nome')->get();
        $fabricantes = Fabricante::orderBy('nome')->get();
        $tiposEquipamento = TipoEquipamento::orderBy('nome')->get();

        return view('documentos.create', compact('categorias', 'fabricantes', 'tiposEquipamento'));
    }

    public function store(StoreDocumentoRequest $request)
    {
        $file = $request->file('arquivo');
        $filePath = null;

        DB::beginTransaction();

        try {
            // 1. Armazenamento do arquivo em subpasta ano/mês dentro do storage/app/documents
            $subFolder = 'documents/' . date('Y/m');
            $filePath = $file->store($subFolder, 'local');

            if (!$filePath) {
                throw new \Exception('Não foi possível armazenar o arquivo no servidor.');
            }

            // 2. Criação do documento
            $documento = Documento::create([
                'titulo' => $request->titulo,
                'categoria_id' => $request->categoria_id,
                'descricao' => $request->descricao,
                'observacao' => $request->observacao,
                'fabricante_id' => in_array($request->tipo_relacionamento, ['fabricante', 'modelo', 'aplicacao']) ? $request->fabricante_id : null,
                'usuario_id' => auth()->id(),
            ]);

            // 3. Vinculo Pivot (Modelo ou Aplicação)
            if ($request->tipo_relacionamento === 'modelo') {
                $documento->modelos()->attach($request->modelo_id);
            } elseif ($request->tipo_relacionamento === 'aplicacao') {
                $documento->aplicacoes()->attach($request->aplicacao_id);
            }

            // 4. Registo da Primeira Versão
            $versao = DocumentoVersao::create([
                'documento_id' => $documento->id,
                'versao' => $request->versao,
                'data_documento' => $request->data_documento,
                'nome_arquivo' => $file->getClientOriginalName(),
                'caminho_arquivo' => $filePath,
                'tamanho_bytes' => $file->getSize(),
                'usuario_id' => auth()->id(),
            ]);

            // 5. Atualização da referência para a Versão Atual
            $documento->update(['versao_atual_id' => $versao->id]);

            DB::commit();

            return redirect()->route('documentos.index')
                ->with('success', 'Documento cadastrado com sucesso.');

        } catch (\Throwable $e) {
            DB::rollBack();

            // Apaga arquivo enviado caso ocorra falha no Banco de Dados
            if ($filePath && Storage::disk('local')->exists($filePath)) {
                Storage::disk('local')->delete($filePath);
            }

            Log::error('Erro ao cadastrar documento: ' . $e->getMessage());

            return back()
                ->withInput()
                ->withErrors(['erro_geral' => 'Ocorreu uma falha interna ao cadastrar o documento. Por favor, tente novamente.']);
        }
    }

    // --- ENDPOINTS AJAX PARA SELECTS DEPENDENTES ---

    public function getTiposEquipamento(Request $request)
    {
        $fabricanteId = $request->query('fabricante_id');
        
        if (!$fabricanteId) {
            return response()->json([]);
        }

        $tipos = TipoEquipamento::whereHas('modelos', function ($q) use ($fabricanteId) {
            $q->where('fabricante_id', $fabricanteId);
        })->orderBy('nome')->get(['id', 'nome']);

        if ($tipos->isEmpty()) {
            $tipos = TipoEquipamento::orderBy('nome')->get(['id', 'nome']);
        }

        return response()->json($tipos);
    }

    public function getModelos(Request $request)
    {
        $fabricanteId = $request->query('fabricante_id');
        
        $modelos = \App\Models\Modelo::where('fabricante_id', $fabricanteId)
            ->orderBy('nome')
            ->orderBy('modelo')
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
            ->orderBy('descricao')
            ->get(['id', 'descricao']);

        return response()->json($aplicacoes);
    }
}