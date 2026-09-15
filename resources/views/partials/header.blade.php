@php $user = auth()->user(); @endphp

<header class="w-full h-16 border-b border-white/10 px-6 md:px-8 flex items-center justify-between bg-[#091322]/90 backdrop-blur-md sticky top-0 z-40">
    <div class="flex items-center gap-6">
        <!-- LOGO -->
        <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
            <span class="text-xl font-black text-white tracking-wider">FERSA</span>
            <span class="text-[10px] px-2 py-0.5 rounded bg-sky-500/20 text-sky-400 font-bold border border-sky-400/30">HUB</span>
        </a>

        <!-- NAVEGAÇÃO BASEADA EM PERMISSÕES -->
        <nav class="hidden md:flex items-center space-x-5 text-sm font-medium border-l border-white/10 pl-6">
            <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'text-sky-400 font-semibold' : 'text-gray-400 hover:text-white' }} transition">
                Início
            </a>

            @if($user && $user->pode('documentos', 'ver'))
                <a href="{{ route('documentos.index') }}" class="{{ request()->routeIs('documentos.*') ? 'text-sky-400 font-semibold' : 'text-gray-400 hover:text-white' }} transition">
                    Documentos
                </a>
            @endif

            @if($user && $user->pode('usuarios', 'ver'))
                <a href="{{ route('usuarios.index') }}" class="{{ request()->routeIs('usuarios.*') ? 'text-sky-400 font-semibold' : 'text-gray-400 hover:text-white' }} transition">
                    Usuários
                </a>
            @endif

            @if($user && $user->pode('vendas', 'ver'))
                <a href="#" class="text-gray-400 hover:text-white transition">Vendas</a>
            @endif

            @if($user && $user->pode('videos', 'ver'))
                <a href="#" class="text-gray-400 hover:text-white transition">Vídeos</a>
            @endif

            @if($user && $user->pode('cursos', 'ver'))
                <a href="#" class="text-gray-400 hover:text-white transition">Cursos</a>
            @endif

            <!-- MENU EXCLUSIVO DO ADMIN GERAL -->
            @if($user && $user->isSuperAdmin())
                <a href="{{ route('perfis.index') }}" class="{{ request()->routeIs('perfis.*') ? 'text-amber-400 font-semibold' : 'text-amber-400/80 hover:text-amber-300' }} transition flex items-center gap-1">
                    Perfis & Permissões
                </a>
            @endif
        </nav>
    </div>

    <!-- INFORMAÇÕES DO USUÁRIO LOGADO & LOGOUT -->
    <div class="flex items-center gap-4">
        <div class="flex items-center gap-3 border-l border-white/10 pl-4">
            <div class="w-8 h-8 rounded-full bg-sky-500/20 border border-sky-400/30 flex items-center justify-center font-bold text-sky-400 text-xs">
                {{ strtoupper(substr($user->nome ?? 'U', 0, 1)) }}
            </div>
            <div class="hidden sm:block text-xs">
                <p class="font-bold text-white leading-none">{{ $user->nome ?? 'Usuário' }}</p>
                <p class="text-[10px] text-sky-400 mt-0.5">
                    {{ $user->perfil->nome ?? 'Sem Perfil' }}
                </p>
            </div>
        </div>

        <!-- BOTÃO SAIR (LOGOUT) -->
        <form action="{{ route('logout') }}" method="POST" class="inline">
            @csrf
            <button type="submit" title="Sair do sistema" class="p-2 rounded-xl bg-white/5 hover:bg-red-500/20 text-gray-400 hover:text-red-400 transition flex items-center justify-center">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
            </button>
        </form>
    </div>
</header>