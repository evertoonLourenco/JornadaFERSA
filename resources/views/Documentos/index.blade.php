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

            <!-- BOTÕES DE AÇÃO ADMINISTRATIVA -->
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

        @if ($errors->any())
            <div class="bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs rounded-xl p-4 space-y-1">
                @foreach ($errors->all() as $error)
                    <p>⚠️ {{ $error }}</p>
                @endforeach
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
                                    <a href="{{ route('documentos.download', $doc->id) }}" 
                                    class="p-2 rounded-lg text-xs font-semibold bg-blue-600/20 text-blue-400 border border-blue-500/30 hover:bg-blue-600/40 transition flex items-center gap-1"
                                    title="Fazer Download">
                                        <span>Download</span>
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

    <!-- MODAIS DE CADASTRO -->
    @if(auth()->check() && (auth()->user()->isSuperAdmin() || auth()->user()->pode('documentos', 'admin')))

        <!-- MODAL: GERENCIAR CATEGORIAS -->
        <div id="modalNovaCategoria" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">🏷️ Gerenciar Categorias</h3>
                    <button type="button" onclick="toggleModal('modalNovaCategoria')" class="text-gray-400 hover:text-white">&times;</button>
                </div>

                <div class="p-5 space-y-5 overflow-y-auto flex-1 custom-scrollbar">
                    <form action="{{ route('categorias.store') }}" method="POST" class="bg-white/5 border border-white/10 p-4 rounded-xl space-y-3">
                        @csrf
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Nome da Categoria *</label>
                            <input type="text" name="nome" required placeholder="Ex: Manuais Técnicos" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Descrição</label>
                            <textarea name="descricao" rows="2" placeholder="Descrição opcional..." class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 resize-none"></textarea>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">
                                Salvar Categoria
                            </button>
                        </div>
                    </form>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-300">Categorias Cadastradas ({{ count($categorias ?? []) }})</h4>
                        @foreach($categorias ?? [] as $categoriaItem)
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex items-center justify-between gap-3 {{ isset($categoriaItem->ativo) && !$categoriaItem->ativo ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-white truncate">{{ $categoriaItem->nome }}</h5>
                                    <p class="text-[11px] text-gray-400 truncate">{{ $categoriaItem->descricao ?? 'Sem descrição' }}</p>
                                </div>
                                
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <form action="{{ route('categorias.toggle', $categoriaItem->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($categoriaItem->ativo ?? true) ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                                            {{ ($categoriaItem->ativo ?? true) ? '🔴 Desligar' : '🟢 Ligar' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('categorias.destroy', $categoriaItem->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir a categoria {{ $categoriaItem->nome }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-xs font-bold bg-rose-600/20 text-rose-400 border border-rose-500/30 hover:bg-rose-600/40 transition" title="Excluir Categoria">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 border-t border-white/10 flex justify-end">
                    <button type="button" onclick="toggleModal('modalNovaCategoria')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Fechar</button>
                </div>
            </div>
        </div>

        <!-- MODAL: GERENCIAR FABRICANTES -->
        <div id="modalNovoFabricante" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">🏭 Gerenciar Fabricantes</h3>
                    <button type="button" onclick="toggleModal('modalNovoFabricante')" class="text-gray-400 hover:text-white">&times;</button>
                </div>

                <div class="p-5 space-y-5 overflow-y-auto flex-1 custom-scrollbar">
                    <form action="{{ route('fabricantes.store') }}" method="POST" class="bg-white/5 border border-white/10 p-4 rounded-xl space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Nome do Fabricante *</label>
                                <input type="text" name="nome" required placeholder="Ex: Bosch, Mahle, SKF..." class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Categoria Vinculada *</label>
                                <select name="categoria_id" required class="w-full bg-[#0c182e] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                                    <option value="" disabled selected>Selecione uma categoria...</option>
                                    @foreach($categoriasAtivas ?? ($categorias ?? []) as $catAtiva)
                                        <option value="{{ $catAtiva->id }}">{{ $catAtiva->nome }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end pt-1">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">
                                Salvar Fabricante
                            </button>
                        </div>
                    </form>

                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-300">Fabricantes Ativos ({{ count($fabricantes ?? []) }})</h4>
                        @forelse($fabricantes ?? [] as $fab)
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex items-center justify-between gap-3 {{ isset($fab->ativo) && !$fab->ativo ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-white truncate">{{ $fab->nome }}</h5>
                                    <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/20 mt-1">
                                        📁 {{ $fab->categoria->nome ?? 'Sem Categoria' }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <form action="{{ route('fabricantes.toggle', $fab->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($fab->ativo ?? true) ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                                            {{ ($fab->ativo ?? true) ? '🔴 Desligar' : '🟢 Ligar' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('fabricantes.destroy', $fab->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o fabricante {{ $fab->nome }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-xs font-bold bg-rose-600/20 text-rose-400 border border-rose-500/30 hover:bg-rose-600/40 transition" title="Excluir Fabricante">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 bg-white/5 rounded-xl text-center text-xs text-gray-400 border border-white/10">
                                Nenhum fabricante cadastrado.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="p-4 border-t border-white/10 flex justify-end">
                    <button type="button" onclick="toggleModal('modalNovoFabricante')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Fechar</button>
                </div>
            </div>
        </div>

        <!-- MODAL: GERENCIAR MODELOS -->
        <div id="modalNovoModelo" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">🚚 Gerenciar Modelos</h3>
                    <button type="button" onclick="toggleModal('modalNovoModelo')" class="text-gray-400 hover:text-white">&times;</button>
                </div>

                <div class="p-5 space-y-5 overflow-y-auto flex-1 custom-scrollbar">
                    <!-- Form Cadastro de Modelo -->
                    <form action="{{ route('modelos.store') }}" method="POST" class="bg-white/5 border border-white/10 p-4 rounded-xl space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            <!-- Categoria -->
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Categoria <span class="text-rose-400">*</span></label>
                                <select name="categoria_id" id="mod_categoria_id" onchange="filtrarFabricantesModelo()" required class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                                    <option value="" disabled selected>Selecione a categoria...</option>
                                    @foreach($categoriasAtivas ?? ($categorias ?? []) as $categoria)
                                        <option value="{{ $categoria->id }}">{{ $categoria->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Fabricante -->
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Fabricante <span class="text-rose-400">*</span></label>
                                <select name="fabricante_id" id="mod_fabricante_id" required disabled class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 disabled:opacity-50">
                                    <option value="" disabled selected>Selecione a categoria primeiro...</option>
                                    @foreach($fabricantes ?? [] as $fab)
                                        <option value="{{ $fab->id }}" data-categoria="{{ $fab->categoria_id ?? '' }}" class="hidden">{{ $fab->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Nome / Modelo -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-300 mb-1">Nome / Código do Modelo <span class="text-rose-400">*</span></label>
                                <input type="text" name="modelo" value="{{ old('modelo') }}" placeholder="Ex: FH 540, Constellation 24.280..." required class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                            </div>

                            <!-- Ano Início -->
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Ano Início</label>
                                <input type="number" name="ano_inicio" value="{{ old('ano_inicio') }}" placeholder="Ex: 2015" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                            </div>

                            <!-- Ano Fim -->
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Ano Fim</label>
                                <input type="number" name="ano_fim" value="{{ old('ano_fim') }}" placeholder="Ex: 2022" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                            </div>

                            <!-- Descrição -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-bold text-gray-300 mb-1">Descrição / Observações</label>
                                <textarea name="descricao" rows="2" placeholder="Informações adicionais sobre o modelo..." class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 resize-none">{{ old('descricao') }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">
                                Salvar Modelo
                            </button>
                        </div>
                    </form>

                    <!-- Listagem de Modelos Cadastrados -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-300">Modelos Cadastrados ({{ count($modelos ?? []) }})</h4>
                        @forelse($modelos ?? [] as $mod)
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex items-center justify-between gap-3 {{ isset($mod->ativo) && !$mod->ativo ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-white truncate">{{ $mod->nome ?? $mod->modelo }}</h5>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                            🏭 {{ $mod->fabricante->nome ?? 'Sem Fabricante' }}
                                        </span>
                                        @if(isset($mod->ano_inicio) || isset($mod->ano_fim))
                                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white/5 text-gray-400 border border-white/10">
                                                📅 {{ $mod->ano_inicio ?? '?' }} - {{ $mod->ano_fim ?? 'Atual' }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <form action="{{ route('modelos.toggle', $mod->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($mod->ativo ?? true) ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                                            {{ ($mod->ativo ?? true) ? '🔴 Desligar' : '🟢 Ligar' }}
                                        </button>
                                    </form>

                                    <form action="{{ route('modelos.destroy', $mod->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir o modelo {{ $mod->nome ?? $mod->modelo }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-xs font-bold bg-rose-600/20 text-rose-400 border border-rose-500/30 hover:bg-rose-600/40 transition" title="Excluir Modelo">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 bg-white/5 rounded-xl text-center text-xs text-gray-400 border border-white/10">
                                Nenhum modelo cadastrado.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="p-4 border-t border-white/10 flex justify-end">
                    <button type="button" onclick="toggleModal('modalNovoModelo')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Fechar</button>
                </div>
            </div>
        </div>

        <!-- MODAL: GERENCIAR APLICAÇÕES -->
        <div id="modalNovaAplicacao" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[85vh]">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">⚙️ Gerenciar Aplicações</h3>
                    <button type="button" onclick="toggleModal('modalNovaAplicacao')" class="text-gray-400 hover:text-white">&times;</button>
                </div>
                
                <div class="p-5 space-y-5 overflow-y-auto flex-1 custom-scrollbar">
                    <!-- Formulário de Cadastro de Aplicação -->
                    <form action="{{ route('aplicacoes.store') }}" method="POST" class="bg-white/5 border border-white/10 p-4 rounded-xl space-y-3">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Categoria *</label>
                                <select id="app_categoria_id" onchange="filtrarFabricantesAplicacao()" required class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                                    <option value="">Selecione...</option>
                                    @foreach($categorias ?? [] as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Fabricante *</label>
                                <select id="app_fabricante_id" onchange="filtrarModelosAplicacao()" required disabled class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 disabled:opacity-50">
                                    <option value="">Aguardando Categoria</option>
                                    @foreach($fabricantes ?? [] as $fab)
                                        <option value="{{ $fab->id }}" data-categoria="{{ $fab->categoria_id ?? '' }}" class="hidden">{{ $fab->nome }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-300 mb-1">Modelo *</label>
                                <select name="modelo_id" id="app_modelo_id" required disabled class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 disabled:opacity-50">
                                    <option value="">Aguardando Fabricante</option>
                                    @foreach($modelos ?? [] as $mod)
                                        <option value="{{ $mod->id }}" data-fabricante="{{ $mod->fabricante_id ?? '' }}" class="hidden">{{ $mod->nome ?? $mod->modelo }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Componente / Descrição da Aplicação *</label>
                            <input type="text" name="componente" required placeholder="Ex: Caixas de Câmbio VT2014, Cubo Traseiro..." class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                        </div>

                        <div class="flex justify-end pt-1">
                            <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">
                                Salvar Aplicação
                            </button>
                        </div>
                    </form>

                    <!-- Listagem de Aplicações Cadastradas -->
                    <div class="space-y-2">
                        <h4 class="text-xs font-bold text-gray-300">Aplicações Cadastradas ({{ count($aplicacoes ?? []) }})</h4>
                        @forelse($aplicacoes ?? [] as $app)
                            <div class="bg-white/5 border border-white/10 rounded-xl p-3 flex items-center justify-between gap-3 {{ isset($app->ativo) && !$app->ativo ? 'opacity-50' : '' }}">
                                <div class="min-w-0">
                                    <h5 class="text-xs font-bold text-white truncate">{{ $app->componente ?? ($app->descricao ?? 'Sem descrição') }}</h5>
                                    <div class="flex flex-wrap items-center gap-1.5 mt-1">
                                        @if(isset($app->modelo))
                                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                                🚚 {{ $app->modelo->nome ?? ($app->modelo->modelo ?? 'Sem Modelo') }}
                                            </span>
                                        @endif
                                        @if(isset($app->modelo->fabricante))
                                            <span class="inline-block px-2 py-0.5 rounded-md text-[10px] font-semibold bg-white/5 text-gray-400 border border-white/10">
                                                🏭 {{ $app->modelo->fabricante->nome }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    <!-- Toggle Ativo/Inativo -->
                                    <form action="{{ route('aplicacoes.toggle', $app->id) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="px-2.5 py-1 rounded-lg text-xs font-bold transition {{ ($app->ativo ?? true) ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' }}">
                                            {{ ($app->ativo ?? true) ? '🔴 Desligar' : '🟢 Ligar' }}
                                        </button>
                                    </form>

                                    <!-- Botão Deletar -->
                                    <form action="{{ route('aplicacoes.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir a aplicação {{ $app->componente ?? $app->descricao }}?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 rounded-lg text-xs font-bold bg-rose-600/20 text-rose-400 border border-rose-500/30 hover:bg-rose-600/40 transition" title="Excluir Aplicação">
                                            🗑️
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <div class="p-4 bg-white/5 rounded-xl text-center text-xs text-gray-400 border border-white/10">
                                Nenhuma aplicação cadastrada.
                            </div>
                        @endforelse
                    </div>
                </div>

                <div class="p-4 border-t border-white/10 flex justify-end">
                    <button type="button" onclick="toggleModal('modalNovaAplicacao')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Fechar</button>
                </div>
            </div>
        </div>

        <!-- MODAL: NOVO DOCUMENTO -->
        <div id="modalNovoDocumento" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
            <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-2xl overflow-hidden shadow-2xl flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-white/10 flex items-center justify-between">
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">📄 Upload de Documento Técnico</h3>
                    <button type="button" onclick="toggleModal('modalNovoDocumento')" class="text-gray-400 hover:text-white">&times;</button>
                </div>

                <form action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" class="p-5 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
                    @csrf
                    
                    <!-- Título do Documento -->
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Título do Documento <span class="text-rose-400">*</span></label>
                        <input type="text" name="titulo" required placeholder="Ex: Manual Técnico de Montagem" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>

                    <!-- Vínculos Técnicos (Encadeados) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3 bg-white/5 p-4 rounded-xl border border-white/10">
                        <div class="md:col-span-2">
                            <span class="text-[11px] font-bold text-sky-400 uppercase tracking-wider block mb-1">Vínculos Técnicos do Documento</span>
                        </div>

                        <!-- 1. Categoria -->
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Categoria <span class="text-rose-400">*</span></label>
                            <select name="categoria_id" id="doc_categoria_id" onchange="filtrarFabricantesDoc()" required class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                                <option value="" disabled selected>Selecione Categoria...</option>
                                @foreach($categoriasAtivas ?? ($categorias ?? []) as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 2. Fabricante -->
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Fabricante</label>
                            <select name="fabricante_id" id="doc_fabricante_id" onchange="filtrarModelosDoc()" disabled class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 disabled:opacity-50">
                                <option value="" selected>Selecione a Categoria primeiro...</option>
                                @foreach($fabricantes ?? [] as $fab)
                                    <option value="{{ $fab->id }}" data-categoria="{{ $fab->categoria_id ?? '' }}" class="hidden">{{ $fab->nome }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 3. Modelo -->
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Modelo</label>
                            <select name="modelo_id" id="doc_modelo_id" onchange="filtrarAplicacoesDoc()" disabled class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 disabled:opacity-50">
                                <option value="" selected>Selecione o Fabricante primeiro...</option>
                                @foreach($modelos ?? [] as $mod)
                                    <option value="{{ $mod->id }}" data-fabricante="{{ $mod->fabricante_id ?? '' }}" class="hidden">{{ $mod->nome ?? $mod->modelo }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- 4. Aplicação -->
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Aplicação</label>
                            <select name="aplicacao_id" id="doc_aplicacao_id" disabled class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 disabled:opacity-50">
                                <option value="" selected>Selecione o Modelo primeiro...</option>
                                @foreach($aplicacoes ?? [] as $app)
                                    <option value="{{ $app->id }}" data-modelo="{{ $app->modelo_id ?? '' }}" class="hidden">{{ $app->componente ?? $app->descricao }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Dados Adicionais -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Versão <span class="text-rose-400">*</span></label>
                            <input type="text" name="versao" required placeholder="Ex: 1.0" value="1.0" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-300 mb-1">Data do Documento</label>
                            <input type="date" name="data_documento" value="{{ date('Y-m-d') }}" class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                        </div>
                    </div>

                    <!-- Upload do Arquivo -->
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Arquivo <span class="text-rose-400">*</span></label>
                        <input type="file" name="arquivo" required class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-gray-300 focus:outline-none focus:border-sky-400 file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-sky-500/20 file:text-sky-400">
                    </div>

                    <!-- Descrição Opcional -->
                    <div>
                        <label class="block text-xs font-bold text-gray-300 mb-1">Descrição / Observações</label>
                        <textarea name="descricao" rows="2" placeholder="Informações complementares do documento..." class="w-full bg-white/5 border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400 resize-none"></textarea>
                    </div>

                    <div class="pt-2 border-t border-white/10 flex justify-end gap-2">
                        <button type="button" onclick="toggleModal('modalNovoDocumento')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold text-xs">Enviar Documento</button>
                    </div>
                </form>
            </div>
        </div>

    @endif

    <!-- JAVASCRIPT GLOBAL -->
    <script>
        function toggleModal(modalId) {
            const modal = document.getElementById(modalId);
            if (modal) {
                modal.classList.toggle('hidden');
            }
        }

        function filtrarFabricantesModelo() {
            const catId = document.getElementById('mod_categoria_id').value;
            const fabSelect = document.getElementById('mod_fabricante_id');
            const options = fabSelect.querySelectorAll('option');

            fabSelect.value = '';
            fabSelect.disabled = !catId;

            options.forEach(opt => {
                if (!opt.value) return;
                if (opt.getAttribute('data-categoria') === catId) {
                    opt.classList.remove('hidden');
                } else {
                    opt.classList.add('hidden');
                }
            });
        }

        function filtrarFabricantesAplicacao() {
            const catId = document.getElementById('app_categoria_id').value;
            const fabSelect = document.getElementById('app_fabricante_id');
            const options = fabSelect.querySelectorAll('option');

            fabSelect.value = '';
            fabSelect.disabled = !catId;

            options.forEach(opt => {
                if (!opt.value) return;
                if (opt.getAttribute('data-categoria') === catId) {
                    opt.classList.remove('hidden');
                } else {
                    opt.classList.add('hidden');
                }
            });

            filtrarModelosAplicacao();
        }

        function filtrarModelosAplicacao() {
            const fabId = document.getElementById('app_fabricante_id').value;
            const modSelect = document.getElementById('app_modelo_id');
            const options = modSelect.querySelectorAll('option');

            modSelect.value = '';
            modSelect.disabled = !fabId;

            options.forEach(opt => {
                if (!opt.value) return;
                if (opt.getAttribute('data-fabricante') === fabId) {
                    opt.classList.remove('hidden');
                } else {
                    opt.classList.add('hidden');
                }
            });
        }
        function filtrarFabricantesDoc() {
    const catId = document.getElementById('doc_categoria_id').value;
    const fabSelect = document.getElementById('doc_fabricante_id');
    const options = fabSelect.querySelectorAll('option');

    fabSelect.value = '';
    fabSelect.disabled = !catId;

    options.forEach(opt => {
        if (!opt.value) return;
        if (opt.getAttribute('data-categoria') === catId) {
            opt.classList.remove('hidden');
        } else {
            opt.classList.add('hidden');
        }
    });

    filtrarModelosDoc();
    }

    function filtrarModelosDoc() {
        const fabId = document.getElementById('doc_fabricante_id').value;
        const modSelect = document.getElementById('doc_modelo_id');
        const options = modSelect.querySelectorAll('option');

        modSelect.value = '';
        modSelect.disabled = !fabId;

        options.forEach(opt => {
            if (!opt.value) return;
            if (opt.getAttribute('data-fabricante') === fabId) {
                opt.classList.remove('hidden');
            } else {
                opt.classList.add('hidden');
            }
        });

        filtrarAplicacoesDoc();
    }

    function filtrarAplicacoesDoc() {
        const modId = document.getElementById('doc_modelo_id').value;
        const appSelect = document.getElementById('doc_aplicacao_id');
        const options = appSelect.querySelectorAll('option');

        appSelect.value = '';
        appSelect.disabled = !modId;

        options.forEach(opt => {
            if (!opt.value) return;
            if (opt.getAttribute('data-modelo') === modId) {
                opt.classList.remove('hidden');
            } else {
                opt.classList.add('hidden');
            }
        });
    }
    </script>
    @include('components.agente-flutuante')
</body>
</html>