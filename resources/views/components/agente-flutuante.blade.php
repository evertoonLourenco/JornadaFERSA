<!-- Botão Flutuante (Canto Inferior Direito) -->
<div class="fixed bottom-6 right-6 z-50">
    <button onclick="toggleWidgetIa()" 
        class="bg-purple-600 hover:bg-purple-500 text-white rounded-full p-4 shadow-2xl flex items-center justify-center border-2 border-purple-400/30 transition-all hover:scale-105 active:scale-95">
        <span class="text-2xl">🤖</span>
    </button>
</div>

<!-- Widget / Chat Flutuante -->
<div id="widgetIa" class="fixed bottom-24 right-6 z-50 hidden w-96 max-w-[calc(100vw-2rem)] bg-slate-900 border border-purple-500/30 rounded-2xl shadow-2xl overflow-hidden flex flex-col h-[520px] transition-all">
    
    <!-- Cabeçalho -->
    <div class="bg-slate-950 p-4 border-b border-slate-800 flex items-center justify-between">
        <div class="flex items-center gap-2">
            <span class="text-xl">🤖</span>
            <div>
                <h4 class="text-sm font-bold text-purple-400">Agente FersaHUB</h4>
                <p class="text-[10px] text-slate-400">Suporte técnico e análise</p>
            </div>
        </div>
        <button onclick="toggleWidgetIa()" class="text-slate-400 hover:text-slate-200 text-lg font-bold px-2">&times;</button>
    </div>

    <!-- Seletor opcional de documento -->
    <div class="bg-slate-900/90 p-2.5 border-b border-slate-800">
        <select id="globalIaDocumento" class="w-full bg-slate-800 border border-slate-700 rounded-lg p-2 text-xs text-slate-300 focus:outline-none focus:border-purple-500">
            <option value="">-- Dúvida geral (sem documento) --</option>
        </select>
    </div>

    <!-- Histórico de Mensagens -->
    <div id="globalIaHistorico" class="flex-1 p-4 overflow-y-auto space-y-3 text-xs text-slate-200">
        <div class="bg-slate-800 p-3 rounded-xl rounded-tl-none border border-slate-700/50 max-w-[85%]">
            Olá! Sou o assistente virtual do FersaHUB. Como posso ajudar você hoje?
        </div>
    </div>

    <!-- Indicador de Carregamento -->
    <div id="globalIaLoading" class="hidden px-4 py-2 text-[11px] text-purple-400 animate-pulse">
        🤖 O Agente está processando...
    </div>

    <!-- Formulário de Envio -->
    <div class="p-3 bg-slate-950 border-t border-slate-800 flex items-center gap-2">
        <input type="text" id="globalIaPergunta" 
            placeholder="Digite sua dúvida ou instrução..."
            onkeydown="if(event.key === 'Enter') enviarPerguntaGlobalIa()"
            class="flex-1 bg-slate-800 border border-slate-700 rounded-lg px-3 py-2 text-xs text-slate-200 focus:outline-none focus:border-purple-500">
        <button id="globalIaBtnEnviar" onclick="enviarPerguntaGlobalIa()" 
            class="bg-purple-600 hover:bg-purple-500 disabled:opacity-50 text-white p-2 rounded-lg text-xs font-semibold transition">
            ➔
        </button>
    </div>
</div>

<script>
    let documentosCarregados = false;

    function toggleWidgetIa() {
        const widget = document.getElementById('widgetIa');
        widget.classList.toggle('hidden');

        if (!widget.classList.contains('hidden') && !documentosCarregados) {
            carregarDocumentosGlobal();
        }
    }

    async function carregarDocumentosGlobal() {
        try {
            const res = await fetch('{{ route("agente.documentos") }}', {
                headers: { 'Accept': 'application/json' }
            });
            if (!res.ok) return;
            const docs = await res.json();
            const select = document.getElementById('globalIaDocumento');

            docs.forEach(doc => {
                const opt = document.createElement('option');
                opt.value = doc.id;
                opt.innerText = '📄 ' + doc.titulo;
                select.appendChild(opt);
            });

            documentosCarregados = true;
        } catch (e) {
            console.error('Erro ao carregar documentos:', e);
        }
    }

    async function enviarPerguntaGlobalIa() {
        const input = document.getElementById('globalIaPergunta');
        const btnEnviar = document.getElementById('globalIaBtnEnviar');
        const pergunta = input.value.trim();
        const docId = document.getElementById('globalIaDocumento').value;
        const historico = document.getElementById('globalIaHistorico');
        const loading = document.getElementById('globalIaLoading');

        if (!pergunta || input.disabled) return;

        // Desabilita controles durante o envio
        input.disabled = true;
        btnEnviar.disabled = true;

        // Exibe a mensagem do usuário via innerText (proteção contra XSS)
        const msgUser = document.createElement('div');
        msgUser.className = 'bg-purple-600 text-white p-3 rounded-xl rounded-tr-none ml-auto max-w-[85%]';
        msgUser.innerText = pergunta;
        historico.appendChild(msgUser);

        input.value = '';
        historico.scrollTop = historico.scrollHeight;
        loading.classList.remove('hidden');

        try {
            const tokenMeta = document.querySelector('meta[name="csrf-token"]');
            const token = tokenMeta ? tokenMeta.getAttribute('content') : '{{ csrf_token() }}';

            const response = await fetch('{{ route("agente.chat") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': token
                },
                body: JSON.stringify({
                    pergunta: pergunta,
                    documento_id: docId || null
                })
            });

            const data = await response.json();
            loading.classList.add('hidden');

            const msgBot = document.createElement('div');
            msgBot.className = 'bg-slate-800 p-3 rounded-xl rounded-tl-none border border-slate-700/50 max-w-[85%] whitespace-pre-line';

            if (response.ok && data.success) {
                msgBot.innerText = data.resposta;
            } else {
                msgBot.className += ' text-rose-400 border-rose-500/40';
                msgBot.innerText = 'Erro: ' + (data.error || `Servidor respondeu com status ${response.status}`);
            }

            historico.appendChild(msgBot);
            historico.scrollTop = historico.scrollHeight;

        } catch (err) {
            loading.classList.add('hidden');
            const msgErr = document.createElement('div');
            msgErr.className = 'bg-slate-800 text-rose-400 p-3 rounded-xl rounded-tl-none border border-rose-500/40 max-w-[85%]';
            msgErr.innerText = 'Falha de comunicação: ' + err.message;
            historico.appendChild(msgErr);
            historico.scrollTop = historico.scrollHeight;
        } finally {
            // Reabilita controles
            input.disabled = false;
            btnEnviar.disabled = false;
            input.focus();
        }
    }
</script>