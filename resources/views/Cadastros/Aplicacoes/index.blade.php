<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aplicações - FERSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 p-6 font-sans text-gray-800">

    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-[#13284c] mb-1 inline-block">← Voltar ao Painel</a>
                <h1 class="text-2xl font-bold text-[#13284c]">Cadastro de Aplicações</h1>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl p-3 mb-4 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form Aplicação -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 h-fit">
                <h2 class="text-sm font-bold text-[#13284c] mb-3">Nova Aplicação</h2>
                <form action="{{ route('aplicacoes.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fabricante *</label>
                        <select id="selectFab" required class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Selecione...</option>
                            @foreach($fabricantes as $fab)
                                <option value="{{ $fab->id }}">{{ $fab->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Modelo *</label>
                        <select name="modelo_id" id="selectMod" required class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Escolha primeiro o fabricante...</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Aplicação / Posição *</label>
                        <input type="text" name="descricao" required placeholder="Ex: Eixo Dianteiro, Caixa Câmbio ZF" class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                    </div>

                    <button type="submit" class="w-full bg-[#13284c] hover:bg-[#0c1b35] text-white py-2 rounded-xl text-xs font-bold shadow-sm transition">
                        Salvar Aplicação
                    </button>
                </form>
            </div>

            <!-- Tabela Aplicações -->
            <div class="md:col-span-2 bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-sm font-bold text-[#13284c] mb-3">Aplicações Cadastradas</h2>
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs text-gray-500 uppercase">
                            <th class="py-2 px-3">Aplicação</th>
                            <th class="py-2 px-3">Modelo / Fabricante</th>
                            <th class="py-2 px-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($aplicacoes as $app)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3 font-semibold text-gray-800">{{ $app->descricao }}</td>
                                <td class="py-2.5 px-3 text-xs text-gray-600">
                                    {{ $app->modelo->nome ?? $app->modelo->modelo ?? 'N/A' }} 
                                    <span class="text-slate-400">({{ $app->modelo->fabricante->nome ?? 'N/A' }})</span>
                                </td>
                                <td class="py-2.5 px-3 text-right">
                                    <form action="{{ route('aplicacoes.destroy', $app->id) }}" method="POST" onsubmit="return confirm('Deseja excluir?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="3" class="text-center py-4 text-xs text-gray-400">Nenhuma aplicação cadastrada.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $aplicacoes->links() }}</div>
            </div>
        </div>
    </div>

    <!-- Script AJAX ajustado para aceitar tanto m.nome quanto m.modelo -->
    <script>
        document.getElementById('selectFab').addEventListener('change', async function() {
            const fabId = this.value;
            const selectMod = document.getElementById('selectMod');
            selectMod.innerHTML = '<option value="">Carregando...</option>';

            if (!fabId) {
                selectMod.innerHTML = '<option value="">Escolha primeiro o fabricante...</option>';
                return;
            }

            try {
                const response = await fetch(`/api/modelos?fabricante_id=${fabId}`);
                const modelos = await response.json();
                selectMod.innerHTML = '<option value="">Selecione o Modelo...</option>';
                
                modelos.forEach(m => {
                    const opt = document.createElement('option');
                    opt.value = m.id;
                    opt.textContent = m.nome || m.modelo; // Aceita 'nome' ou 'modelo'
                    selectMod.appendChild(opt);
                });
            } catch(e) {
                selectMod.innerHTML = '<option value="">Erro ao carregar modelos</option>';
            }
        });
    </script>

</body>
</html>