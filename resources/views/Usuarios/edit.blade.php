<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Usuário - FERSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4 font-sans text-gray-800">

    <div class="bg-white rounded-[25px] w-full max-w-lg p-8 shadow-xl border border-slate-200">
        
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-[#13284c]">Editar Usuário</h1>
                <p class="text-xs text-gray-500 mt-1">Altere os dados de {{ $usuario->nome }}</p>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 text-sm rounded-2xl p-4 mb-6">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('usuarios.update', $usuario->id) }}" method="POST" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label for="nome" class="block font-semibold text-gray-700 text-sm mb-1">Nome Completo</label>
                <input type="text" name="nome" id="nome" value="{{ old('nome', $usuario->nome) }}" required class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c]">
            </div>

            <div>
                <label for="email" class="block font-semibold text-gray-700 text-sm mb-1">E-mail</label>
                <input type="email" name="email" id="email" value="{{ old('email', $usuario->email) }}" required class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c]">
            </div>

            <div>
                <label for="perfil_id" class="block font-semibold text-gray-700 text-sm mb-1">Perfil de Acesso</label>
                <select name="perfil_id" id="perfil_id" required class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                    @foreach ($perfis as $perfil)
                        <option value="{{ $perfil->id }}" {{ old('perfil_id', $usuario->perfil_id) == $perfil->id ? 'selected' : '' }}>
                            {{ $perfil->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-2 border-t border-slate-200">
                <p class="text-xs text-gray-500 mb-2">Preencha os campos de senha apenas se desejar alterá-la:</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block font-semibold text-gray-700 text-sm mb-1">Nova Senha</label>
                        <input type="password" name="password" id="password" placeholder="••••••••" class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4">
                    </div>
                    <div>
                        <label for="password_confirmation" class="block font-semibold text-gray-700 text-sm mb-1">Confirmar Senha</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" placeholder="••••••••" class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4">
                    </div>
                </div>
            </div>

            <div class="pt-4 flex items-center justify-between gap-4">
                <a href="{{ route('usuarios.index') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium">
                    ← Cancelar
                </a>
                <button type="submit" class="bg-[#13284c] hover:bg-[#0c1b35] text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition">
                    Atualizar Usuário
                </button>
            </div>
        </form>

    </div>

</body>
</html>