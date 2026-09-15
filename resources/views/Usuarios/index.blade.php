<!DOCTYPE html>
<html lang="pt-BR" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gerenciamento de Usuários - FERSA HUB</title>
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
                    <h1 class="text-2xl font-bold text-white">Administração de Usuários</h1>
                    <span class="bg-sky-500/10 text-sky-400 text-xs px-2.5 py-0.5 rounded-full border border-sky-400/20 font-bold">
                        {{ $usuarios->count() }} Usuários
                    </span>
                </div>
                <p class="text-xs text-gray-400 mt-1">Gerencie permissões, perfis de acesso, matrículas e contas temporárias.</p>
            </div>

            <!-- BOTÃO NOVO USUÁRIO: Apenas para Administradores do Módulo -->
            @if(auth()->user()->pode('usuarios', 'admin'))
                <button onclick="toggleModal('modalNovoUsuario')" class="bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold py-2.5 px-4 rounded-xl text-xs flex items-center justify-center gap-2 shadow-lg shadow-sky-500/20 transition">
                    + Novo Usuário
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

        <!-- TABELA DE USUÁRIOS -->
        <div class="glass-panel rounded-2xl overflow-hidden border border-white/10">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white/5 border-b border-white/10 text-[11px] text-gray-400 uppercase tracking-wider">
                            <th class="py-4 px-5">Nome / E-mail</th>
                            <th class="py-4 px-5">Matrícula</th>
                            <th class="py-4 px-5">Perfil de Acesso</th>
                            <th class="py-4 px-5">Status / Expiração</th>
                            <th class="py-4 px-5 text-right">Ações</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5 text-xs">
                        @forelse ($usuarios as $user)
                            <tr class="hover:bg-white/[0.02]">
                                <td class="py-4 px-5">
                                    <div class="font-bold text-white">{{ $user->nome }}</div>
                                    <div class="text-[11px] text-gray-400 font-mono mt-0.5">{{ $user->email }}</div>
                                </td>
                                <td class="py-4 px-5 font-mono text-gray-300">
                                    {{ $user->matricula ?? '-' }}
                                </td>
                                <td class="py-4 px-5">
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ optional($user->perfil)->is_admin_geral ? 'bg-amber-500/10 text-amber-400 border border-amber-400/20' : 'bg-sky-500/10 text-sky-400 border border-sky-400/20' }}">
                                        {{ $user->perfil->nome ?? 'Sem Perfil' }}
                                    </span>
                                </td>
                                <td class="py-4 px-5">
                                    @if($user->provisorio || $user->is_provisorio)
                                        <div class="flex items-center gap-1.5 text-amber-400">
                                            <span class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></span>
                                            <span class="font-bold text-[11px]">Provisório</span>
                                        </div>
                                        <div class="text-[10px] text-gray-400">
                                            Validade: {{ $user->data_expiracao ? \Carbon\Carbon::parse($user->data_expiracao)->format('d/m/Y') : 'Sem data' }}
                                        </div>
                                    @else
                                        <span class="text-gray-400">Permanente</span>
                                    @endif
                                </td>
                                <td class="py-4 px-5 text-right">
                                    <!-- AÇÕES: Exibidas apenas se o usuário logado tiver nível Admin em Usuários -->
                                    @if(auth()->user()->pode('usuarios', 'admin'))
                                        <div class="flex items-center justify-end gap-2">
                                            <button onclick="editarUsuario({{ json_encode($user) }})" class="px-3 py-1.5 rounded-xl bg-white/5 hover:bg-white/10 text-gray-300 font-medium transition">
                                                Editar
                                            </button>
                                            @if(auth()->id() !== $user->id)
                                                <form action="{{ route('usuarios.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
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
                                <td colspan="5" class="py-12 text-center text-gray-400 text-xs">
                                    Nenhum usuário cadastrado até o momento.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- MODAIS APENAS PARA ADMINISTRADORES DO MÓDULO -->
    @if(auth()->user()->pode('usuarios', 'admin'))

    <!-- MODAL: NOVO USUÁRIO -->
    <div id="modalNovoUsuario" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">👤 Cadastrar Novo Usuário</h3>
                <button onclick="toggleModal('modalNovoUsuario')" class="text-gray-400 hover:text-white">&times;</button>
            </div>

            <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Nome Completo *</label>
                    <input type="text" name="nome" required placeholder="Ex: João Silva" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">E-mail *</label>
                    <input type="email" name="email" required placeholder="joao@fersa.com.br" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Senha *</label>
                    <input type="password" name="senha" required placeholder="••••••••" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Perfil de Acesso *</label>
                    <select name="perfil_id" id="novo_perfil_id" required onchange="toggleCamposColaborador('novo')" class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                        <option value="">Selecione um perfil...</option>
                        @foreach($perfis as $perfil)
                            <option value="{{ $perfil->id }}" data-is-admin-geral="{{ $perfil->is_admin_geral ? '1' : '0' }}">
                                {{ $perfil->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- CAMPO MATRÍCULA -->
                <div id="novo_box_matricula">
                    <label class="block text-xs font-bold text-gray-300 mb-1">Matrícula do Colaborador</label>
                    <input type="text" name="matricula" id="novo_matricula" placeholder="Ex: FERSA-9982" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <!-- OPÇÃO DE CONTA PROVISÓRIA -->
                <div class="pt-2 border-t border-white/10 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_provisorio" id="novo_is_provisorio" value="1" onchange="toggleCamposProvisorio('novo')" class="rounded border-white/10 bg-white/5 text-sky-500 focus:ring-0">
                        <span class="text-xs font-bold text-amber-400">Conta Provisória / Temporária</span>
                    </label>

                    <div id="novo_box_expiracao" class="hidden">
                        <label class="block text-xs font-bold text-gray-300 mb-1">Data de Expiração *</label>
                        <input type="date" name="data_expiracao" id="novo_data_expiracao" class="w-full bg-[#091322] border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-white/10">
                    <button type="button" onclick="toggleModal('modalNovoUsuario')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold rounded-xl text-xs shadow-lg shadow-sky-500/20">Salvar Usuário</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL: EDITAR USUÁRIO -->
    <div id="modalEditarUsuario" class="fixed inset-0 bg-black/80 backdrop-blur-sm z-50 flex items-center justify-center hidden p-4">
        <div class="bg-[#0c182e] border border-white/15 rounded-2xl w-full max-w-md p-6 space-y-4 shadow-2xl">
            <div class="flex items-center justify-between border-b border-white/10 pb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">✏️ Editar Usuário</h3>
                <button onclick="toggleModal('modalEditarUsuario')" class="text-gray-400 hover:text-white">&times;</button>
            </div>

            <form id="formEditarUsuario" method="POST" class="space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Nome Completo *</label>
                    <input type="text" id="edit_nome" name="nome" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">E-mail *</label>
                    <input type="email" id="edit_email" name="email" required class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Alterar Senha (Opcional)</label>
                    <input type="password" name="senha" placeholder="Deixe em branco para não alterar" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-300 mb-1">Perfil de Acesso *</label>
                    <select name="perfil_id" id="edit_perfil_id" required onchange="toggleCamposColaborador('edit')" class="w-full bg-[#091322] border border-white/10 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                        <option value="">Selecione um perfil...</option>
                        @foreach($perfis as $perfil)
                            <option value="{{ $perfil->id }}" data-is-admin-geral="{{ $perfil->is_admin_geral ? '1' : '0' }}">
                                {{ $perfil->nome }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- CAMPO MATRÍCULA -->
                <div id="edit_box_matricula">
                    <label class="block text-xs font-bold text-gray-300 mb-1">Matrícula do Colaborador</label>
                    <input type="text" name="matricula" id="edit_matricula" placeholder="Ex: FERSA-9982" class="w-full bg-white/5 border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                </div>

                <!-- OPÇÃO DE CONTA PROVISÓRIA -->
                <div class="pt-2 border-t border-white/10 space-y-3">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_provisorio" id="edit_is_provisorio" value="1" onchange="toggleCamposProvisorio('edit')" class="rounded border-white/10 bg-white/5 text-sky-500 focus:ring-0">
                        <span class="text-xs font-bold text-amber-400">Conta Provisória / Temporária</span>
                    </label>

                    <div id="edit_box_expiracao" class="hidden">
                        <label class="block text-xs font-bold text-gray-300 mb-1">Data de Expiração *</label>
                        <input type="date" name="data_expiracao" id="edit_data_expiracao" class="w-full bg-[#091322] border border-white/10 rounded-xl px-4 py-2 text-xs text-white focus:outline-none focus:border-sky-400">
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 pt-3 border-t border-white/10">
                    <button type="button" onclick="toggleModal('modalEditarUsuario')" class="px-4 py-2 text-xs font-bold text-gray-400 hover:text-white">Cancelar</button>
                    <button type="submit" class="px-5 py-2 bg-sky-500 hover:bg-sky-400 text-[#091322] font-extrabold rounded-xl text-xs shadow-lg shadow-sky-500/20">Atualizar Usuário</button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- JAVASCRIPT DE REATIVIDADE -->
    <script>
        function toggleModal(id) {
            document.getElementById(id)?.classList.toggle('hidden');
        }

        // Oculta a matrícula se for Administrador Geral, mantendo visível para os demais perfis
        function toggleCamposColaborador(prefix) {
            const selectPerfil = document.getElementById(`${prefix}_perfil_id`);
            if (!selectPerfil) return;
            const selectedOption = selectPerfil.options[selectPerfil.selectedIndex];
            const isAdminGeral = selectedOption?.getAttribute('data-is-admin-geral') === '1';
            const boxMatricula = document.getElementById(`${prefix}_box_matricula`);

            if (boxMatricula) {
                if (isAdminGeral) {
                    boxMatricula.classList.add('hidden');
                } else {
                    boxMatricula.classList.remove('hidden');
                }
            }
        }

        // Exibe ou oculta a data de expiração caso marque como conta provisória
        function toggleCamposProvisorio(prefix) {
            const isProvisorioInput = document.getElementById(`${prefix}_is_provisorio`);
            const boxExpiracao = document.getElementById(`${prefix}_box_expiracao`);

            if (isProvisorioInput && boxExpiracao) {
                if (isProvisorioInput.checked) {
                    boxExpiracao.classList.remove('hidden');
                } else {
                    boxExpiracao.classList.add('hidden');
                }
            }
        }

        // Preenche os dados no modal de Edição
        function editarUsuario(user) {
            document.getElementById('edit_nome').value = user.nome || '';
            document.getElementById('edit_email').value = user.email || '';
            document.getElementById('edit_perfil_id').value = user.perfil_id || '';
            document.getElementById('edit_matricula').value = user.matricula || '';

            // Verifica as propriedades de conta provisória
            const isProvisorio = !!(user.provisorio || user.is_provisorio);
            document.getElementById('edit_is_provisorio').checked = isProvisorio;

            if (user.data_expiracao) {
                const dataFormatted = user.data_expiracao.split('T')[0];
                document.getElementById('edit_data_expiracao').value = dataFormatted;
            } else {
                document.getElementById('edit_data_expiracao').value = '';
            }

            toggleCamposColaborador('edit');
            toggleCamposProvisorio('edit');

            document.getElementById('formEditarUsuario').action = `/usuarios/${user.id}`;

            toggleModal('modalEditarUsuario');
        }
    </script>
</body>
</html>