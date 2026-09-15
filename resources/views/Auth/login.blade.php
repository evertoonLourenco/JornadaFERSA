<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - FERSA Bearings</title>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body {
            background-color: #0d1a30;
            overflow: hidden;
        }
        /* Estilo para suavizar o efeito 3D no card */
        .tilt-card {
            transform-style: preserve-3d;
            transition: transform 0.1s ease-out;
        }
        .glow-effect {
            box-shadow: 0 0 25px rgba(255, 255, 255, 0.2);
        }
    </style>
</head>
<body class="min-h-screen flex items-center justify-center p-4 relative select-none">

    <!-- CANVAS PARA O FUNDO DE PONTINHOS / PARTÍCULAS INTERATIVAS -->
    <canvas id="bg-canvas" class="fixed inset-0 pointer-events-none z-0"></canvas>

    <!-- CARD DE LOGIN COM EFEITO 3D TILT -->
    <div id="login-card" class="tilt-card relative z-10 bg-white rounded-[35px] w-full max-w-[390px] p-8 shadow-2xl flex flex-col items-center border border-slate-100">
        
        <!-- Logo Circular FERSA -->
        <div id="fersa-logo" class="w-28 h-28 rounded-full bg-gradient-to-b from-[#13284c] to-[#0c1b35] flex items-center justify-center shadow-lg mb-4 border-4 border-white transition-all duration-500 cursor-pointer hover:scale-105">
            <span class="text-white font-black tracking-widest text-2xl italic select-none">
                FERSA
            </span>
        </div>

        <!-- Mensagem de Boas-Vindas -->
        <p id="welcome-msg" class="text-xs text-gray-500 font-semibold mb-4 text-center">
            Digite suas credenciais para acessar
        </p>

        <!-- Formulário de Login -->
        <form action="{{ route('login.perform') }}" method="POST" class="w-full space-y-4">
            @csrf

            <!-- Exibição de Erros -->
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 text-xs rounded-2xl p-3 text-center">
                    {{ $errors->first() }}
                </div>
            @endif

            <!-- Campo Usuário -->
            <div>
                <label for="email" class="block text-center font-bold text-gray-900 text-base mb-1">
                    Usuário
                </label>
                <input 
                    type="email" 
                    name="email" 
                    id="email" 
                    value="{{ old('email') }}" 
                    required 
                    autofocus
                    placeholder="admin@fersa.com"
                    class="w-full bg-[#d0d5dd] text-gray-800 text-center font-medium rounded-full py-3 px-4 focus:outline-none focus:ring-2 focus:ring-[#13284c] focus:bg-slate-100 transition duration-200 placeholder-gray-500"
                >
            </div>

            <!-- Campo Senha -->
            <div>
                <label for="password" class="block text-center font-bold text-gray-900 text-base mb-1">
                    Senha
                </label>
                <div class="relative">
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        required 
                        placeholder="••••••••"
                        class="w-full bg-[#d0d5dd] text-gray-800 text-center font-medium rounded-full py-3 px-10 focus:outline-none focus:ring-2 focus:ring-[#13284c] focus:bg-slate-100 transition duration-200"
                    >
                    <!-- Botão Olho (Mostrar/Ocultar Senha) -->
                    <button type="button" id="toggle-password" class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-600 hover:text-black focus:outline-none p-1">
                        <svg id="eye-icon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Esqueceu a Senha & Botão de Preenchimento Rápido -->
            <div class="flex flex-col items-center space-y-2 pt-1">
                <a href="#" class="text-xs text-gray-600 hover:text-[#13284c] hover:underline font-medium">
                    Esqueceu sua senha?
                </a>
                
                <!-- Botão Interativo: Preenchimento Automático Animado -->
                <button type="button" id="btn-quick-fill" class="text-[11px] bg-slate-100 hover:bg-slate-200 text-slate-700 px-3 py-1 rounded-full font-semibold transition border border-slate-300 flex items-center gap-1">
                    ⚡ Auto-preencher Admin
                </button>
            </div>

            <!-- Botão Entrar -->
            <div class="pt-2">
                <button 
                    type="submit" 
                    class="w-full bg-[#13284c] hover:bg-[#0c1b35] text-white font-bold py-3 rounded-full shadow-lg transition duration-200 transform active:scale-95"
                >
                    Entrar
                </button>
            </div>
        </form>

    </div>

    <!-- SCRIPT JAVASCRIPT: PONTINHOS E INTERAÇÕES -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {

            /* ============================================================
               1. CANVAS DE PONTINHOS / REDE DE PARTÍCULAS NO FUNDO
               ============================================================ */
            const canvas = document.getElementById('bg-canvas');
            const ctx = canvas.getContext('2d');

            function resizeCanvas() {
                canvas.width = window.innerWidth;
                canvas.height = window.innerHeight;
            }
            resizeCanvas();
            window.addEventListener('resize', resizeCanvas);

            // Posição do mouse
            const mouse = { x: null, y: null, radius: 140 };

            window.addEventListener('mousemove', (e) => {
                mouse.x = e.clientX;
                mouse.y = e.clientY;
            });

            // Criar partículas (pontinhos)
            const particlesArray = [];
            const numberOfParticles = Math.floor((window.innerWidth * window.innerHeight) / 12000);

            class Particle {
                constructor() {
                    this.x = Math.random() * canvas.width;
                    this.y = Math.random() * canvas.height;
                    this.size = Math.random() * 2 + 1;
                    this.speedX = (Math.random() - 0.5) * 0.8;
                    this.speedY = (Math.random() - 0.5) * 0.8;
                }

                update() {
                    this.x += this.speedX;
                    this.y += this.speedY;

                    // Rebater nas bordas
                    if (this.x > canvas.width || this.x < 0) this.speedX *= -1;
                    if (this.y > canvas.height || this.y < 0) this.speedY *= -1;

                    // Interação de repulsão com o cursor
                    const dx = mouse.x - this.x;
                    const dy = mouse.y - this.y;
                    const distance = Math.sqrt(dx * dx + dy * dy);

                    if (distance < mouse.radius) {
                        const angle = Math.atan2(dy, dx);
                        const force = (mouse.radius - distance) / mouse.radius;
                        this.x -= Math.cos(angle) * force * 3;
                        this.y -= Math.sin(angle) * force * 3;
                    }
                }

                draw() {
                    ctx.fillStyle = 'rgba(255, 255, 255, 0.4)';
                    ctx.beginPath();
                    ctx.arc(this.x, this.y, this.size, 0, Math.PI * 2);
                    ctx.fill();
                }
            }

            for (let i = 0; i < numberOfParticles; i++) {
                particlesArray.push(new Particle());
            }

            // Loop de Animação
            function animate() {
                ctx.clearRect(0, 0, canvas.width, canvas.height);

                for (let i = 0; i < particlesArray.length; i++) {
                    particlesArray[i].update();
                    particlesArray[i].draw();

                    // Conectar pontinhos próximos com linhas finas
                    for (let j = i; j < particlesArray.length; j++) {
                        const dx = particlesArray[i].x - particlesArray[j].x;
                        const dy = particlesArray[i].y - particlesArray[j].y;
                        const distance = Math.sqrt(dx * dx + dy * dy);

                        if (distance < 110) {
                            ctx.strokeStyle = `rgba(255, 255, 255, ${0.15 - distance / 700})`;
                            ctx.lineWidth = 0.6;
                            ctx.beginPath();
                            ctx.moveTo(particlesArray[i].x, particlesArray[i].y);
                            ctx.lineTo(particlesArray[j].x, particlesArray[j].y);
                            ctx.stroke();
                        }
                    }
                }
                requestAnimationFrame(animate);
            }

            animate();

            /* ============================================================
               2. EFEITO 3D TILT NO CARD (Inclinamento com o Mouse)
               ============================================================ */
            const card = document.getElementById('login-card');
            
            window.addEventListener('mousemove', (e) => {
                const { innerWidth, innerHeight } = window;
                const x = (e.clientX - innerWidth / 2) / (innerWidth / 2);
                const y = (e.clientY - innerHeight / 2) / (innerHeight / 2);
                
                const rotateX = y * -12; 
                const rotateY = x * 12; 
                
                card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg)`;
            });

            /* ============================================================
               3. MOSTRAR / OCULTAR SENHA
               ============================================================ */
            const passwordInput = document.getElementById('password');
            const togglePasswordBtn = document.getElementById('toggle-password');
            const eyeIcon = document.getElementById('eye-icon');

            togglePasswordBtn.addEventListener('click', () => {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';

                if (isPassword) {
                    eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.003 10.003 0 0110.123 3.937m-2.14 2.825a3 3 0 11-4.243-4.243M3 3l18 18" />`;
                } else {
                    eyeIcon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />`;
                }
            });

            /* ============================================================
               4. AUTO-PREENCHIMENTO COM DIGITAÇÃO ANIMADA
               ============================================================ */
            const btnQuickFill = document.getElementById('btn-quick-fill');
            const emailInput = document.getElementById('email');

            btnQuickFill.addEventListener('click', () => {
                const targetEmail = "admin@fersa.com";
                const targetPass = "senha123";
                
                emailInput.value = "";
                passwordInput.value = "";
                
                let i = 0;
                let j = 0;

                const typeEmail = setInterval(() => {
                    if (i < targetEmail.length) {
                        emailInput.value += targetEmail.charAt(i);
                        i++;
                    } else {
                        clearInterval(typeEmail);
                        const typePass = setInterval(() => {
                            if (j < targetPass.length) {
                                passwordInput.value += targetPass.charAt(j);
                                j++;
                            } else {
                                clearInterval(typePass);
                                triggerLogoSpin();
                            }
                        }, 50);
                    }
                }, 40);
            });

            /* ============================================================
               5. REAÇÃO E GIRO DO LOGO FERSA
               ============================================================ */
            const logo = document.getElementById('fersa-logo');
            const welcomeMsg = document.getElementById('welcome-msg');

            function triggerLogoSpin() {
                logo.style.transform = 'rotate(360deg) scale(1.1)';
                logo.classList.add('glow-effect');
                setTimeout(() => {
                    logo.style.transform = 'rotate(0deg) scale(1)';
                    logo.classList.remove('glow-effect');
                }, 600);
            }

            logo.addEventListener('click', triggerLogoSpin);

            passwordInput.addEventListener('focus', () => {
                welcomeMsg.innerText = "Sua senha está segura conosco 🔒";
                welcomeMsg.classList.add('text-[#13284c]');
            });

            passwordInput.addEventListener('blur', () => {
                welcomeMsg.innerText = "Digite suas credenciais para acessar";
                welcomeMsg.classList.remove('text-[#13284c]');
            });
        });
    </script>
</body>
</html>