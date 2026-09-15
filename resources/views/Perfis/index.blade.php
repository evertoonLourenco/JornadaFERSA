<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfis de Acesso & Permissões - FERSA HUB</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        ::-webkit-scrollbar { width: 6px; height: 6px; }
        ::-webkit-scrollbar-track { background: #091322; }
        ::-webkit-scrollbar-thumb { background: #1e3d73; border-radius: 4px; }
        .glass-panel { background: rgba(255, 255, 255, 0.03); backdrop-filter: blur(16px); border: 1px solid rgba(255, 255, 255, 0.08); }
    </style>
</head>
<body class="bg-[#091322] text-white min-h-full font-sans antialiased flex flex-col">

    <!-- CABEÇALHO UNIFICADO -->
    @include('partials.header')

    <!-- CONTEÚDO PRINCIPAL -->
    <main class="flex-1 max-w-7xl w-full mx-auto p-6 md:p-8 space-y-6">

        <!-- PAINEL SUPERIOR -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 glass-panel p-6 rounded-2xl">
            <div>
                <div class="flex items-center gap-2">
                    <h1 class="text-2xl font-bold text-white">Perfis & Permissões (RBAC)</h1>
                    <span class="bg-sky-500/10 text-sky-400 text-xs px-2.5 py-0.5 rounded-full border border-sky-400/20 font-bold">
                        {{ $perfis->count() }} Perfis
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Defina os níveis de acesso de cada grupo aos módulos do sistema.</p>
            </div>

            @if(auth()->user()->isSuperAdmin())
                <button onclick="toggleModal('modalNovoPerfil')" class="bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg shadow-sky-500/20 transition">
                    + Criar Novo Perfil
                </button>
            @endif
        </div>

        <!-- MENSAGENS DE FEEDBACK -->
        @if (session('success'))
            <div class="bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs rounded-xl p-4 flex items-center gap-2">
                <span>✅</span> {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-500/10 border border-red-500/30 text-red-400 text-xs rounded-xl p-4">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- TABELA DE PERFIS -->
        <div class="glass-panel rounded-2xl overflow-hidden border border-white/10">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-[11px] text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-5">Perfil</th>
                            <th class="py-4 px-5">Usuários Vinculados</th>
                            <th class="py-4 px-5">Permissões nos Módulos</th>
                            <th class="py-4 px-5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs">
                        @forelse ($perfis as $perfil)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-4 px-5">
                                    <div class="font-bold text-white flex items-center gap-2">
                                        {{ $perfil->nome }}
                                        @if($perfil->is_admin_geral)
                                            <span class="bg-amber-500/10 text-amber-400 text-[10px] px-2 py-0.5 rounded border border-amber-400/20 font-bold">
                                                Super Admin
                                            </span>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-4 px-5 font-mono text-gray-300">
                                    <span class="bg-white/5 px-2.5 py-1 rounded-lg border border-white/10">
                                        {{ $perfil->usuarios_count ?? 0 }} usuário(s)
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    @if($perfil->is_admin_geral)
                                        <span class="text-amber-400 text-[11px] font-semibold">Acesso total irrestrito a todos os módulos</span>
                                    @else
                                        <div class="flex flex-wrap gap-1.5">
                                            @foreach($modulos as $mod)
                                                @php
                                                    $perm = $perfil->permissoes[$mod] ?? 'nenhum';
                                                @endphp
                                                @if($perm !== 'nenhum')
                                                    <span class="px-2 py-0.5 rounded text-[10px] font-mono border {{ $perm === 'admin' ? 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20' : 'bg-sky-500/10 text-sky-400 border-sky-500/20' }}">
                                                        {{ ucfirst($mod) }}: <strong>{{ strtoupper($perm) }}</strong>
                                                    </span>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right">
                                    @if(auth()->user()->isSuperAdmin())
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="editarPerfil({{ json_encode($perfil) }})" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-medium transition">
                                                Editar
                                            </button>
                                            @if(!$perfil->is_admin_geral)
                                                <form action="{{ route('perfis.destroy', $perfil->id) }}" method="POST" onsubmit="return confirm('Deseja excluir este perfil?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="px-3 py-1.5 rounded-xl bg-red-500/10 hover:bg-red-500/20 text-red-400 font-medium transition">
                                                        Excluir
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    @else
                                        <span class="text-[11px] text-gray-500 italic">Somente leitura</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-12 text-center text-gray-400 text-xs">
                                    Nenhum perfil cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAL: NOVO PERFIL -->
    <div id="modalNovoPerfil" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-lg p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">🛡️ Novo Perfil de Acesso</h3>
                <button onclick="toggleModal('modalNovoPerfil')" class="text-gray-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('perfis.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Nome do Perfil *</label>
                    <input type="text" name="nome" required placeholder="Ex: Financeiro / Vendas" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div class="space-y-2 pt-2 border-t border-white/10">
                    <label class="block text-xs font-bold text-gray-300">Permissões por Módulo</label>

                    <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                        @foreach($modulos as $mod)
                            <div class="flex items-center justify-between bg-white/5 p-2.5 rounded-xl border border-white/5">
                                <span class="text-xs font-bold capitalize text-gray-200">{{ $mod }}</span>
                                <select name="permissoes[{{ $mod }}]" class="bg-[#091322] border border-white/10 rounded-lg px-2.5 py-1 text-xs text-white focus:outline-none focus:border-sky-400">
                                    <option value="nenhum">Nenhum Acesso</option>
                                    <option value="ver">Visualizador (Ver)</option>
                                    <option value="admin">Administrador (Total)</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-white/10">
                    <button type="button" onclick="toggleModal('modalNovoPerfil')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold rounded-xl text-xs shadow-lg shadow-sky-500/20">Salvar Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR PERFIL -->
    <div id="modalEditarPerfil" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-lg p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">✏️ Editar Perfil de Acesso</h3>
                <button onclick="toggleModal('modalEditarPerfil')" class="text-gray-400 hover:text-white">&times;</button>
            </div>

            <form id="formEditarPerfil" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Nome do Perfil *</label>
                    <input type="text" id="edit_nome" name="nome" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div class="space-y-2 pt-2 border-t border-white/10">
                    <label class="block text-xs font-bold text-gray-300">Permissões por Módulo</label>

                    <div class="space-y-2 max-h-60 overflow-y-auto pr-2">
                        @foreach($modulos as $mod)
                            <div class="flex items-center justify-between bg-white/5 p-2.5 rounded-xl border border-white/5">
                                <span class="text-xs font-bold capitalize text-gray-200">{{ $mod }}</span>
                                <select id="edit_perm_{{ $mod }}" name="permissoes[{{ $mod }}]" class="bg-[#091322] border border-white/10 rounded-lg px-2.5 py-1 text-xs text-white focus:outline-none focus:border-sky-400">
                                    <option value="nenhum">Nenhum Acesso</option>
                                    <option value="ver">Visualizador (Ver)</option>
                                    <option value="admin">Administrador (Total)</option>
                                </select>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-white/10">
                    <button type="button" onclick="toggleModal('modalEditarPerfil')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold rounded-xl text-xs shadow-lg shadow-sky-500/20">Atualizar Perfil</button>
                </div>
            </form>
        </div>
    </div>

    <!-- JAVASCRIPT -->
    <script>
        function toggleModal(id) {
            document.getElementById(id)?.classList.toggle('hidden');
        }

        function editarPerfil(perfil) {
            document.getElementById('edit_nome').value = perfil.nome || '';

            const modulos = @json($modulos);
            const permissoes = perfil.permissoes || {};

            modulos.forEach(mod => {
                const select = document.getElementById(`edit_perm_${mod}`);
                if (select) {
                    select.value = permissoes[mod] || 'nenhum';
                }
            });

            document.getElementById('formEditarPerfil').action = `/perfis/${perfil.id}`;
            toggleModal('modalEditarPerfil');
        }
    </script>
</body>
</html>