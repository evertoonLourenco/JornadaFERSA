<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modelos - FERSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 p-6 font-sans text-gray-800">

    <div class="max-w-5xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('dashboard') }}" class="text-xs font-bold text-slate-500 hover:text-[#13284c] mb-1 inline-block">← Voltar ao Painel</a>
                <h1 class="text-2xl font-bold text-[#13284c]">Cadastro de Modelos</h1>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 text-xs rounded-xl p-3 mb-4 font-semibold">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Form Modelo -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 h-fit">
                <h2 class="text-sm font-bold text-[#13284c] mb-3">Novo Modelo</h2>
                <form action="{{ route('modelos.store') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Fabricante *</label>
                        <select name="fabricante_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Selecione...</option>
                            @foreach($fabricantes as $fab)
                                <option value="{{ $fab->id }}">{{ $fab->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Tipo de Equipamento *</label>
                        <select name="tipo_equipamento_id" required class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Selecione...</option>
                            @foreach($tiposEquipamento as $tipo)
                                <option value="{{ $tipo->id }}">{{ $tipo->nome }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Nome do Modelo *</label>
                        <input type="text" name="nome" required placeholder="Ex: FH 540, R 450" class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2 px-3 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                    </div>

                    <button type="submit" class="w-full bg-[#13284c] hover:bg-[#0c1b35] text-white py-2 rounded-xl text-xs font-bold shadow-sm transition">
                        Salvar Modelo
                    </button>
                </form>
            </div>

            <!-- Tabela Modelos -->
            <div class="md:col-span-2 bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <h2 class="text-sm font-bold text-[#13284c] mb-3">Modelos Cadastrados</h2>
                <table class="w-full text-left border-collapse text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs text-gray-500 uppercase">
                            <th class="py-2 px-3">Modelo</th>
                            <th class="py-2 px-3">Fabricante</th>
                            <th class="py-2 px-3">Tipo</th>
                            <th class="py-2 px-3 text-right">Ação</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($modelos as $mod)
                            <tr class="hover:bg-slate-50">
                                <td class="py-2.5 px-3 font-semibold text-gray-800">{{ $mod->nome ?? $mod->modelo }}</td>
                                <td class="py-2.5 px-3 text-xs">{{ $mod->fabricante->nome ?? 'N/A' }}</td>
                                <td class="py-2.5 px-3 text-xs text-gray-500">{{ $mod->tipoEquipamento->nome ?? 'N/A' }}</td>
                                <td class="py-2.5 px-3 text-right">
                                    <form action="{{ route('modelos.destroy', $mod->id) }}" method="POST" onsubmit="return confirm('Deseja excluir este modelo?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 text-xs font-bold">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-4 text-xs text-gray-400">Nenhum modelo cadastrado.</td></tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="mt-4">{{ $modelos->links() }}</div>
            </div>
        </div>
    </div>

</body>
</html>