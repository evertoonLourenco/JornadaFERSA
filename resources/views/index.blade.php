<!DOCTYPE html>
<html lang="pt-BR" class="h-full overflow-hidden">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FersaHUB - Portal Principal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
    
    <style>
        /* Estilização da barra de rolagem */
        ::-webkit-scrollbar {
            width: 6px;
        }
        ::-webkit-scrollbar-track {
            background: #091322;
        }
        ::-webkit-scrollbar-thumb {
            background: #1e3d73;
            border-radius: 4px;
        }

        /* Glassmorphism */
        .glass-panel {
            background: rgba(255, 255, 255, 0.03);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
        }

        /* Estilização das setas verticais da direita */
        .vertical-nav-btn {
            width: 44px;
            height: 44px;
            background: rgba(15, 31, 61, 0.8);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            color: #ffffff;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .vertical-nav-btn:hover {
            background: #38bdf8;
            color: #091322;
            border-color: #38bdf8;
            transform: scale(1.08);
        }
        .vertical-nav-btn.swiper-button-disabled {
            opacity: 0.25;
            cursor: not-allowed;
            transform: none !important;
        }

        /* Marcadores verticais da paginação */
        .swiper-pagination-bullet {
            background: #ffffff;
            opacity: 0.3;
            width: 8px;
            height: 8px;
            margin: 6px 0 !important;
            transition: all 0.3s ease;
        }
        .swiper-pagination-bullet-active {
            background: #38bdf8 !important;
            opacity: 1;
            height: 24px;
            border-radius: 4px;
        }
    </style>
</head>
<body class="bg-[#091322] text-white h-full w-full font-sans antialiased select-none flex overflow-hidden">

    <aside class="w-64 h-full bg-[#0c182e] border-r border-white/10 flex flex-col justify-between p-6 z-30 shrink-0">
        <div class="space-y-8">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-2">
                <span class="text-2xl font-black text-white tracking-wider">FERSA</span>
                <span class="text-[10px] px-2 py-0.5 rounded bg-sky-500/20 text-sky-400 font-bold border border-sky-400/30">HUB</span>
            </a>

            <div class="space-y-3">
                <p class="text-[10px] font-bold uppercase tracking-widest text-gray-400 px-1">Navegação da Página</p>
                <nav class="space-y-2 text-sm font-medium">
                    <button onclick="goToSlide(0)" class="nav-item w-full text-left px-4 py-3 rounded-xl transition flex items-center justify-between bg-sky-500/10 text-sky-400 font-bold border border-sky-400/20">
                        <span>01. Destaque Vídeos</span>
                        <span class="text-xs opacity-60">01</span>
                    </button>
                    <button onclick="goToSlide(1)" class="nav-item w-full text-left px-4 py-3 rounded-xl transition flex items-center justify-between text-gray-400 hover:text-white hover:bg-white/5">
                        <span>02. Vídeo Técnico 02</span>
                        <span class="text-xs opacity-60">02</span>
                    </button>
                    <button onclick="goToSlide(2)" class="nav-item w-full text-left px-4 py-3 rounded-xl transition flex items-center justify-between text-gray-400 hover:text-white hover:bg-white/5">
                        <span>03. Giro de Notícias</span>
                        <span class="text-xs opacity-60">03</span>
                    </button>
                    <button onclick="goToSlide(3)" class="nav-item w-full text-left px-4 py-3 rounded-xl transition flex items-center justify-between text-gray-400 hover:text-white hover:bg-white/5">
                        <span>04. Curiosidades</span>
                        <span class="text-xs opacity-60">04</span>
                    </button>
                </nav>
            </div>
        </div>

        <div class="pt-4 border-t border-white/10 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-full bg-white/10 flex items-center justify-center font-bold text-sky-400 border border-white/15">
                    {{ strtoupper(substr(auth()->user()->nome ?? 'U', 0, 1)) }}
                </div>
                <div class="text-xs">
                    <p class="font-bold text-white truncate max-w-[110px]">{{ auth()->user()->nome ?? 'Usuário' }}</p>
                    <p class="text-gray-400 text-[10px]">Conectado</p>
                </div>
            </div>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="text-gray-400 hover:text-red-400 p-1 transition" title="Sair">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1m0-10V5"/></svg>
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 h-full flex flex-col relative overflow-hidden">

    @include('partials.header')

        <main class="flex-1 w-full h-[calc(100vh-64px)] relative">
            <div class="swiper mainVerticalSwiper w-full h-full">
                <div class="swiper-wrapper">

                    <div class="swiper-slide w-full h-full relative bg-black">
                        <div class="video-container w-full h-full relative cursor-pointer group">
                            <video class="w-full h-full object-cover" loop playsinline preload="metadata">
                                <source src="https://assets.mixkit.co/videos/preview/mixkit-mechanic-repairing-a-car-engine-41581-large.mp4" type="video/mp4">
                            </video>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/30 p-10 flex flex-col justify-between pointer-events-none">
                                <div>
                                    <span class="glass-panel px-4 py-1.5 rounded-full text-xs font-bold uppercase text-sky-300 border border-sky-400/30 inline-flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-sky-400 animate-ping"></span>
                                        Série Técnica #01
                                    </span>
                                </div>
                                <div class="max-w-3xl space-y-3">
                                    <h1 class="text-3xl md:text-5xl font-black text-white leading-tight">Ajuste de Folga em Rolamentos Cônicos Taper</h1>
                                    <p class="text-sm md:text-base text-gray-300">Aprenda o passo a passo para realizar o pré-carregamento correto e garantir a durabilidade estendida.</p>
                                </div>
                            </div>

                            <div class="play-btn absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition">
                                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-2xl border border-white/40 transform group-hover:scale-110 transition">
                                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-[#091322] fill-current ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide w-full h-full relative bg-black">
                        <div class="video-container w-full h-full relative cursor-pointer group">
                            <video class="w-full h-full object-cover" loop playsinline preload="metadata">
                                <source src="https://assets.mixkit.co/videos/preview/mixkit-hands-of-a-mechanic-working-with-a-tool-41580-large.mp4" type="video/mp4">
                            </video>

                            <div class="absolute inset-0 bg-gradient-to-t from-black/90 via-black/20 to-black/30 p-10 flex flex-col justify-between pointer-events-none">
                                <div>
                                    <span class="glass-panel px-4 py-1.5 rounded-full text-xs font-bold uppercase text-amber-300 border border-amber-400/30 inline-flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full bg-amber-400 animate-ping"></span>
                                        Série Técnica #02
                                    </span>
                                </div>
                                <div class="max-w-3xl space-y-3">
                                    <h1 class="text-3xl md:text-5xl font-black text-white leading-tight">Inspeção Prévia do Cubo de Roda</h1>
                                    <p class="text-sm md:text-base text-gray-300">Procedimentos para verificação de desgaste e torque de aperto na linha pesada.</p>
                                </div>
                            </div>

                            <div class="play-btn absolute inset-0 flex items-center justify-center bg-black/20 group-hover:bg-black/10 transition">
                                <div class="w-20 h-20 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center shadow-2xl border border-white/40 transform group-hover:scale-110 transition">
                                    <div class="w-14 h-14 bg-white rounded-full flex items-center justify-center shadow-lg">
                                        <svg class="w-7 h-7 text-[#091322] fill-current ml-1" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide w-full h-full p-10 flex flex-col justify-center bg-[#091322]">
                        <div class="max-w-5xl mx-auto w-full space-y-8">
                            <div>
                                <span class="text-xs font-bold text-sky-400 uppercase tracking-widest">Giro de Notícias</span>
                                <h2 class="text-3xl font-extrabold text-white mt-1">Destaques Mundiais</h2>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <article class="glass-panel p-6 rounded-3xl space-y-3">
                                    <span class="text-[10px] font-bold text-purple-400 bg-purple-500/10 px-2.5 py-1 rounded-full uppercase border border-purple-400/20">Ciência</span>
                                    <h3 class="text-xl font-bold text-white">Sinais de Rádio Espaciais</h3>
                                    <p class="text-xs text-gray-400 leading-relaxed">Astrônomos detectaram novas rajadas de rádio vindas de uma galáxia distante a milhões de anos-luz.</p>
                                </article>

                                <article class="glass-panel p-6 rounded-3xl space-y-3">
                                    <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2.5 py-1 rounded-full uppercase border border-emerald-400/20">Meio Ambiente</span>
                                    <h3 class="text-xl font-bold text-white">Ilhas de Conservação Marinha</h3>
                                    <p class="text-xs text-gray-400 leading-relaxed">Novas reservas ambientais estão sendo criadas no Pacífico para preservar ecossistemas de corais.</p>
                                </article>
                            </div>
                        </div>
                    </div>

                    <div class="swiper-slide w-full h-full p-10 flex flex-col justify-center bg-[#070f1b]">
                        <div class="max-w-5xl mx-auto w-full space-y-8">
                            <div>
                                <span class="text-xs font-bold text-amber-400 uppercase tracking-widest">Tecnologia & História</span>
                                <h2 class="text-3xl font-extrabold text-white mt-1">Curiosidades do Mundo</h2>
                            </div>

                            <div class="glass-panel p-8 rounded-3xl space-y-4">
                                <h3 class="text-2xl font-bold text-white">A Evolução dos Supercomputadores</h3>
                                <p class="text-sm text-gray-300 leading-relaxed">Dos primeiros relés mecânicos da década de 1940 até os processadores quânticos modernos, a capacidade de processamento multiplicou-se por bilhões de vezes.</p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="absolute right-8 top-1/2 -translate-y-1/2 z-30 flex flex-col items-center gap-3">
                    <button class="vertical-prev vertical-nav-btn" title="Anterior">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
                    </button>
                    
                    <div class="swiper-pagination-vertical !relative !translate-y-0 my-2"></div>

                    <button class="vertical-next vertical-nav-btn" title="Próximo">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                </div>

            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
    <script>
        const titles = [
            "Ajuste de Folga em Rolamentos",
            "Inspeção do Cubo de Roda",
            "Giro de Notícias Mundiais",
            "Curiosidades de Tecnologia"
        ];

        // Inicializa o Swiper Vertical
        const swiper = new Swiper('.mainVerticalSwiper', {
            direction: 'vertical',
            slidesPerView: 1,
            spaceBetween: 0,
            mousewheel: true,
            keyboard: { enabled: true },
            speed: 700,
            pagination: {
                el: '.swiper-pagination-vertical',
                clickable: true,
            },
            navigation: {
                nextEl: '.vertical-next',
                prevEl: '.vertical-prev',
            },
            on: {
                slideChange: function () {
                    // Atualiza título da seção ativa no header
                    document.getElementById('current-slide-title').innerText = titles[this.activeIndex] || "";

                    // Pausa vídeos não ativos
                    document.querySelectorAll('video').forEach(v => {
                        v.pause();
                        const container = v.closest('.video-container');
                        if(container) {
                            const btn = container.querySelector('.play-btn');
                            if(btn) btn.style.opacity = '1';
                        }
                    });

                    // Atualiza o destaque na sidebar da esquerda
                    document.querySelectorAll('.nav-item').forEach((item, index) => {
                        if (index === this.activeIndex) {
                            item.className = "nav-item w-full text-left px-4 py-3 rounded-xl transition flex items-center justify-between bg-sky-500/10 text-sky-400 font-bold border border-sky-400/20";
                        } else {
                            item.className = "nav-item w-full text-left px-4 py-3 rounded-xl transition flex items-center justify-between text-gray-400 hover:text-white hover:bg-white/5";
                        }
                    });
                }
            }
        });

        // Clique no menu lateral para ir diretamente ao slide
        function goToSlide(index) {
            swiper.slideTo(index);
        }

        // Play/Pause do Vídeo ao clicar
        document.querySelectorAll('.video-container').forEach(container => {
            const video = container.querySelector('video');
            const playBtn = container.querySelector('.play-btn');

            container.addEventListener('click', () => {
                if (video.paused) {
                    video.play();
                    playBtn.style.opacity = '0';
                } else {
                    video.pause();
                    playBtn.style.opacity = '1';
                }
            });
        });
    </script>
    @include('components.agente-flutuante')
</body>
</html>