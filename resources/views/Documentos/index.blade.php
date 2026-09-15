<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Central de Documentos Técnicos - FERSA HUB</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #091322; }
        ::-webkit-scrollbar-thumb { background: #1e3d73; border-radius: 4px; }
        
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }
    </style>
</head>
<body class="bg-[#091322] text-white min-h-full font-sans antialiased flex flex-col">

    <!-- CABEÇALHO GLOBAL DA APLICAÇÃO -->
    @include('partials.header')

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 md:p-8 space-y-6">

        <!-- PAINEL CENTRAL / TÍTULO -->
        <div class="glass-panel p-6 rounded-2xl flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-white tracking-tight">Central de Documentos Técnicos</h1>
                    <span class="bg-sky-500/10 text-sky-400 text-xs px-2.5 py-0.5 rounded-full border border-sky-400/20 font-bold">
                        {{ count($documentos ?? []) }} Arquivos
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Consulte e gerencie manuais, fichas técnicas, catálogos e aplicações homologadas FERSA.</p>
            </div>

            <!-- BOTÕES DE AÇÃO ADMINISTRATIVA (ADMIN GERAL OU PERMISSÃO NO MÓDULO) -->
            @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->pode('documentos', 'admin')))
                <div class="flex flex-wrap items-center gap-2">
                    <button onclick="toggleModal('modalNovaCategoria')" class="bg-white/5 hover:bg-white/10 text-white text-xs font-bold py-2 px-3 rounded-xl border border-white/10 transition">
                        + Categoria
                    </button>
                    <button onclick="toggleModal('modalNovoFabricante')" class="bg-white/5 hover:bg-white/10 text-white text-xs font-bold py-2 px-3 rounded-xl border border-white/10 transition">
                        + Fabricante
                    </button>
                    <button onclick="toggleModal('modalNovoModelo')" class="bg-white/5 hover:bg-white/10 text-white text-xs font-bold py-2 px-3 rounded-xl border border-white/10 transition">
                        + Modelo
                    </button>
                    <button onclick="toggleModal('modalNovaAplicacao')" class="bg-white/5 hover:bg-white/10 text-white text-xs font-bold py-2 px-3 rounded-xl border border-white/10 transition">
                        + Aplicação
                    </button>
                    <button onclick="toggleModal('modalNovoDocumento')" class="bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold py-2 px-4 rounded-xl text-xs flex items-center gap-1.5 shadow-lg shadow-sky-500/20 transition">
                        <span>+ Novo Documento</span>
                    </button>
                </div>
            @endif
        </div>

        <!-- NOTIFICAÇÕES -->
        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-xl p-4 flex items-center gap-3">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- BARRA DE PESQUISA E FILTROS -->
        <form method="GET" action="{{ route('documentos.index') }}" class="glass-panel p-4 rounded-2xl grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
            <div class="md:col-span-4">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Buscar Documento</label>
                <input type="text" name="busca" value="{{ request('busca') }}" placeholder="Nome do documento, código..." class="w-full bg-white/5 border border-white/10 rounded-xl px-3.5 py-2 text-xs text-white placeholder-gray-500 focus:outline-none focus:border-sky-400">
            </div>

            <div class="md:col-span-3">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Categoria</label>
                <select name="categoria_id" class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                    <option value="">Todas as Categorias</option>
                    @foreach($categorias ?? [] as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>{{ $cat->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-3">
                <label class="block text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">Fabricante</label>
                <select name="fabricante_id" class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                    <option value="">Todos os Fabricantes</option>
                    @foreach($fabricantes ?? [] as $fab)
                        <option value="{{ $fab->id }}" {{ request('fabricante_id') == $fab->id ? 'selected' : '' }}>{{ $fab->nome }}</option>
                    @endforeach
                </select>
            </div>

            <div class="md:col-span-2 flex items-center gap-2">
                <button type="submit" class="flex-1 bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 border border-sky-400/30 font-bold py-2 rounded-xl text-xs transition flex items-center justify-center gap-1.5">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    <span>Filtrar</span>
                </button>
                <a href="{{ route('documentos.index') }}" class="px-3 py-2 bg-white/5 hover:bg-white/10 text-gray-400 hover:text-white rounded-xl text-xs font-bold border border-white/10 transition">Limpar</a>
            </div>
        </form>

        <!-- TABELA DE DOCUMENTOS -->
        <div class="glass-panel rounded-2xl overflow-hidden border border-white/10">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-[10px] text-gray-400 uppercase tracking-wider font-semibold">
                            <th class="py-3.5 px-5">Documento</th>
                            <th class="py-3.5 px-5">Categoria</th>
                            <th class="py-3.5 px-5">Vínculos Técnicos (Fabricante / Modelo / Aplicação)</th>
                            <th class="py-3.5 px-5">Versão</th>
                            <th class="py-3.5 px-5">Atualizado Em</th>
                            <th class="py-3.5 px-5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs text-gray-300">
                        @forelse($documentos ?? [] as $doc)
                            <tr class="hover:bg-white/[0.02] transition">
                                <td class="py-4 px-5 font-bold text-white flex items-center gap-2">
                                    <span class="text-sky-400">📄</span>
                                    {{ $doc->titulo ?? $doc->nome }}
                                </td>
                                <td class="py-4 px-5 text-gray-300">
                                    {{ $doc->categoria->nome ?? '-' }}
                                </td>
                                <td class="py-4 px-5 text-gray-400">
                                    {{ $doc->fabricante->nome ?? '-' }} / {{ $doc->modelo->nome ?? '-' }} / {{ $doc->aplicacao->componente ?? ($doc->aplicacao->descricao ?? '-') }}
                                </td>
                                <td class="py-4 px-5 font-mono text-xs">
                                    v{{ $doc->versao ?? '1.0' }}
                                </td>
                                <td class="py-4 px-5 text-gray-400">
                                    {{ \Carbon\Carbon::parse($doc->updated_at)->format('d/m/Y H:i') }}
                                </td>
                                <td class="py-4 px-5 text-right">
                                    <a href="{{ asset('storage/' . $doc->arquivo) }}" target="_blank" class="p-2 rounded-xl bg-sky-500/10 hover:bg-sky-500/20 text-sky-400 border border-sky-400/20 transition inline-block">
                                        Download
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center text-gray-400">
                                    <div class="max-w-sm mx-auto space-y-3">
                                        <div class="w-12 h-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center mx-auto text-xl">
                                            📁
                                        </div>
                                        <div>
                                            <p class="font-bold text-white text-sm">Nenhum documento cadastrado</p>
                                            <p class="text-xs text-gray-500 mt-1">Utilize os botões acima para cadastrar documentos ou novas entidades.</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAIS DE CADASTRO (LIBERADOS PARA SUPER ADMIN OU GESTORES DE DOCUMENTOS) -->
    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->pode('documentos', 'admin')))

        <!-- MODAL: NOVA CATEGORIA -->
        <div id="modalNovaCategoria" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">🏷️ Nova Categoria</h3>
                    <button onclick="toggleModal('modalNovaCategoria')" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('categorias.store') }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Nome da Categoria *</label>
                        <input type="text" name="nome" required placeholder="Ex: Manuais Técnicos, Fichas de Aplicação..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/10">
                        <button type="button" onclick="toggleModal('modalNovaCategoria')" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: NOVO FABRICANTE -->
        <div id="modalNovoFabricante" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">🏭 Novo Fabricante</h3>
                    <button onclick="toggleModal('modalNovoFabricante')" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('fabricantes.store') }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Nome do Fabricante *</label>
                        <input type="text" name="nome" required placeholder="Ex: Scania, Volvo, Mercedes-Benz..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/10">
                        <button type="button" onclick="toggleModal('modalNovoFabricante')" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: NOVO MODELO -->
        <div id="modalNovoModelo" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">🚚 Novo Modelo</h3>
                    <button onclick="toggleModal('modalNovoModelo')" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('modelos.store') }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Fabricante *</label>
                        <select name="fabricante_id" required class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                            <option value="">Selecione um Fabricante</option>
                            @foreach($fabricantes ?? [] as $fab)
                                <option value="{{ $fab->id }}">{{ $fab->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Nome do Modelo *</label>
                        <input type="text" name="nome" required placeholder="Ex: FH 540, R 450..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/10">
                        <button type="button" onclick="toggleModal('modalNovoModelo')" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: NOVA APLICAÇÃO -->
        <div id="modalNovaAplicacao" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-md overflow-hidden shadow-2xl">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">⚙️ Nova Aplicação</h3>
                    <button onclick="toggleModal('modalNovaAplicacao')" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('aplicacoes.store') }}" method="POST" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Modelo Relacionado *</label>
                        <select name="modelo_id" required class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                            <option value="">Selecione um Modelo</option>
                            @foreach($modelos ?? [] as $mod)
                                <option value="{{ $mod->id }}">{{ $mod->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Componente / Posição *</label>
                        <input type="text" name="componente" required placeholder="Ex: Eixo Dianteiro, Cubo Traseiro..." class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Descrição Técnica *</label>
                        <input type="text" name="descricao" required placeholder="Ex: Kit Rolamento Especial FERSA" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/10">
                        <button type="button" onclick="toggleModal('modalNovaAplicacao')" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">Salvar</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL: NOVO DOCUMENTO -->
        <div id="modalNovoDocumento" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-xl overflow-hidden shadow-2xl">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">📄 Cadastrar Novo Documento</h3>
                    <button onclick="toggleModal('modalNovoDocumento')" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Título do Documento *</label>
                        <input type="text" name="titulo" required placeholder="Ex: Manual de Montagem Rolamento R-800" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Categoria *</label>
                            <select name="categoria_id" required class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                                <option value="">Selecione a Categoria</option>
                                @foreach($categorias ?? [] as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Versão *</label>
                            <input type="text" name="versao" value="1.0" required placeholder="Ex: 1.0, 2.1" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Fabricante</label>
                            <select name="fabricante_id" class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                                <option value="">Opcional</option>
                                @foreach($fabricantes ?? [] as $fab)
                                    <option value="{{ $fab->id }}">{{ $fab->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Modelo</label>
                            <select name="modelo_id" class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                                <option value="">Opcional</option>
                                @foreach($modelos ?? [] as $mod)
                                    <option value="{{ $mod->id }}">{{ $mod->nome }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Aplicação</label>
                            <select name="aplicacao_id" class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2.5 text-xs text-white focus:outline-none focus:border-sky-400">
                                <option value="">Opcional</option>
                                @foreach($aplicacoes ?? [] as $app)
                                    <option value="{{ $app->id }}">{{ $app->componente ?? $app->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Arquivo PDF / Anexo *</label>
                        <input type="file" name="arquivo" required accept=".pdf,.doc,.docx" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-400 hover:file:bg-sky-500/20">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-white/10">
                        <button type="button" onclick="toggleModal('modalNovoDocumento')" class="px-4 py-2 rounded-xl text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs shadow-lg shadow-sky-500/20">Enviar Documento</button>
                    </div>
                </form>
            </div>
        </div>

    @endif

    <script>
        function toggleModal(id) {
            const modal = document.getElementById(id);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }
    </script>
</body>
</html>