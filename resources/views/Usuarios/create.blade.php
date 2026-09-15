<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Usuário - FERSA Bearings</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 flex items-center justify-center p-4 font-sans text-gray-800">

    <div class="bg-white rounded-[25px] w-full max-w-lg p-8 shadow-xl border border-slate-200">
        
        <!-- Cabeçalho -->
        <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
            <div>
                <h1 class="text-2xl font-bold text-[#13284c]">Cadastrar Usuário</h1>
                <p class="text-xs text-gray-500 mt-1">Preencha os dados abaixo para criar um novo acesso</p>
            </div>
            <div class="w-12 h-12 rounded-full bg-[#13284c] flex items-center justify-center text-white font-black italic text-xs">
                FERSA
            </div>
        </div>

        <!-- Mensagem de Sucesso -->
        @if (session('success'))
            <div class="bg-emerald-100 border border-emerald-300 text-emerald-800 text-sm rounded-2xl p-4 mb-6 font-medium text-center">
                {{ session('success') }}
            </div>
        @endif

        <!-- Mensagens de Erro de Validação -->
        @if ($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-700 text-sm rounded-2xl p-4 mb-6 font-medium">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Formulário -->
        <form action="{{ route('usuarios.store') }}" method="POST" class="space-y-4">
            @csrf

            <!-- Nome Completo -->
            <div>
                <label for="nome" class="block font-semibold text-gray-700 text-sm mb-1">Nome Completo</label>
                <input 
                    type="text" 
                    name="nome" 
                    id="nome" 
                    value="{{ old('nome') }}" 
                    required 
                    placeholder="Ex: Carlos Silva"
                    class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c] focus:bg-white transition"
                >
            </div>

            <!-- E-mail -->
            <div>
                <label for="email" class="block font-semibold text-gray-700 text-sm mb-1">E-mail de Acesso</label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    required 
                    placeholder="carlos@fersa.com"
                    class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c] focus:bg-white transition"
                >
            </div>

            <!-- Perfil de Acesso -->
            <div>
                <label for="perfil_id" class="block font-semibold text-gray-700 text-sm mb-1">Perfil de Acesso</label>
                <select 
                    name="perfil_id" 
                    id="perfil_id" 
                    required 
                    class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c] focus:bg-white transition"
                >
                    <option value="" disabled selected>Selecione um perfil...</option>
                    @foreach ($perfis as $perfil)
                        <option value="{{ $perfil->id }}" {{ old('perfil_id') == $perfil->id ? 'selected' : '' }}>
                            {{ $perfil->nome }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Senha -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="password" class="block font-semibold text-gray-700 text-sm mb-1">Senha</label>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c] focus:bg-white transition"
                    >
                </div>

                <div>
                    <label for="password_confirmation" class="block font-semibold text-gray-700 text-sm mb-1">Confirmar Senha</label>
                    <input 
                        type="password" 
                        name="password_confirmation" 
                        id="password_confirmation" 
                        required 
                        placeholder="••••••••"
                        class="w-full bg-slate-100 border border-slate-300 text-gray-800 rounded-xl py-2.5 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c] focus:bg-white transition"
                    >
                </div>
            </div>

            <!-- Ações -->
            <div class="pt-4 flex items-center justify-between gap-4">
                <a href="{{ route('login') }}" class="text-sm text-gray-500 hover:text-gray-800 font-medium transition">
                    ← Voltar ao Login
                </a>

                <button 
                    type="submit" 
                    class="bg-[#13284c] hover:bg-[#0c1b35] text-white font-bold py-2.5 px-6 rounded-xl shadow-md transition duration-200 active:scale-95"
                >
                    Salvar Usuário
                </button>
            </div>
        </form>

    </div>

</body>
</html>