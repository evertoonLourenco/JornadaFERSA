<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar documento - FERSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 p-4 md:p-8 font-sans text-gray-800">

    <div class="max-w-4xl mx-auto">
        
        <!-- Topo e Navegação -->
        <div class="flex items-center justify-between mb-6">
            <div>
                <a href="{{ route('documentos.index') }}" class="text-xs font-semibold text-slate-500 hover:text-[#13284c] flex items-center gap-1 mb-1 transition">
                    ← Voltar para Documentos
                </a>
                <h1 class="text-2xl font-bold text-[#13284c]">Cadastrar documento</h1>
                <p class="text-sm text-gray-500">Adicione manuais, fichas técnicas ou catálogos e vincule aos equipamentos.</p>
            </div>
        </div>

        @if ($errors->has('erro_geral'))
            <div class="bg-red-50 border border-red-300 text-red-800 text-sm rounded-xl p-4 mb-6 shadow-sm">
                {{ $errors->first('erro_geral') }}
            </div>
        @endif

        <form id="formDocumento" action="{{ route('documentos.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- SEÇÃO 1: INFORMAÇÕES DO DOCUMENTO -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-[#13284c] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 bg-slate-100 text-[#13284c] rounded-lg text-xs font-bold flex items-center justify-center">1</span>
                    Informações do Documento
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    
                    <div class="md:col-span-2">
                        <label for="titulo" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Título do Documento *
                        </label>
                        <input type="text" name="titulo" id="titulo" value="{{ old('titulo') }}" required placeholder="Ex: Manual de Aplicação Volvo FH" class="w-full bg-slate-50 border @error('titulo') border-red-500 @else border-slate-300 @enderror rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                        @error('titulo')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="categoria_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Categoria *
                        </label>
                        <select name="categoria_id" id="categoria_id" required class="w-full bg-slate-50 border @error('categoria_id') border-red-500 @else border-slate-300 @enderror rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="" disabled {{ old('categoria_id') ? '' : 'selected' }}>Selecione a Categoria...</option>
                            @foreach ($categorias as $cat)
                                <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                    {{ $cat->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('categoria_id')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="hidden md:block"></div>

                    <div>
                        <label for="descricao" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Descrição
                        </label>
                        <textarea name="descricao" id="descricao" rows="3" placeholder="Insira um resumo do conteúdo do documento..." class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">{{ old('descricao') }}</textarea>
                    </div>

                    <div>
                        <label for="observacao" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Observação
                        </label>
                        <textarea name="observacao" id="observacao" rows="3" placeholder="Observações internas..." class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">{{ old('observacao') }}</textarea>
                    </div>

                </div>
            </div>

            <!-- SEÇÃO 2: RELACIONAMENTO -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-[#13284c] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 bg-slate-100 text-[#13284c] rounded-lg text-xs font-bold flex items-center justify-center">2</span>
                    Relacionamento
                </h2>

                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-3">
                    Este documento está relacionado a: *
                </label>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">
                    @php $rel = old('tipo_relacionamento', 'geral'); @endphp

                    <label class="border rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:bg-slate-50 transition border-slate-300 has-[:checked]:border-[#13284c] has-[:checked]:bg-slate-50 has-[:checked]:ring-2 has-[:checked]:ring-[#13284c]">
                        <input type="radio" name="tipo_relacionamento" value="geral" {{ $rel === 'geral' ? 'checked' : '' }} class="text-[#13284c] focus:ring-[#13284c]">
                        <div>
                            <span class="block text-sm font-bold text-gray-800">Geral</span>
                            <span class="text-[10px] text-gray-500">Sem vínculo</span>
                        </div>
                    </label>

                    <label class="border rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:bg-slate-50 transition border-slate-300 has-[:checked]:border-[#13284c] has-[:checked]:bg-slate-50 has-[:checked]:ring-2 has-[:checked]:ring-[#13284c]">
                        <input type="radio" name="tipo_relacionamento" value="fabricante" {{ $rel === 'fabricante' ? 'checked' : '' }} class="text-[#13284c] focus:ring-[#13284c]">
                        <div>
                            <span class="block text-sm font-bold text-gray-800">Fabricante</span>
                            <span class="text-[10px] text-gray-500">À marca</span>
                        </div>
                    </label>

                    <label class="border rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:bg-slate-50 transition border-slate-300 has-[:checked]:border-[#13284c] has-[:checked]:bg-slate-50 has-[:checked]:ring-2 has-[:checked]:ring-[#13284c]">
                        <input type="radio" name="tipo_relacionamento" value="modelo" {{ $rel === 'modelo' ? 'checked' : '' }} class="text-[#13284c] focus:ring-[#13284c]">
                        <div>
                            <span class="block text-sm font-bold text-gray-800">Modelo</span>
                            <span class="text-[10px] text-gray-500">Ao modelo</span>
                        </div>
                    </label>

                    <label class="border rounded-xl p-3 flex items-center gap-2 cursor-pointer hover:bg-slate-50 transition border-slate-300 has-[:checked]:border-[#13284c] has-[:checked]:bg-slate-50 has-[:checked]:ring-2 has-[:checked]:ring-[#13284c]">
                        <input type="radio" name="tipo_relacionamento" value="aplicacao" {{ $rel === 'aplicacao' ? 'checked' : '' }} class="text-[#13284c] focus:ring-[#13284c]">
                        <div>
                            <span class="block text-sm font-bold text-gray-800">Aplicação</span>
                            <span class="text-[10px] text-gray-500">À aplicação</span>
                        </div>
                    </label>
                </div>

                <div id="dynamicFieldsContainer" class="space-y-4 pt-2 border-t border-slate-100 hidden">
                    
                    <div id="fieldFabricante" class="hidden">
                        <label for="fabricante_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Fabricante *
                        </label>
                        <select name="fabricante_id" id="fabricante_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Selecione o Fabricante...</option>
                            @foreach ($fabricantes as $fab)
                                <option value="{{ $fab->id }}" {{ old('fabricante_id') == $fab->id ? 'selected' : '' }}>
                                    {{ $fab->nome }}
                                </option>
                            @endforeach
                        </select>
                        @error('fabricante_id')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="fieldTipoEquipamento" class="hidden">
                        <div class="flex items-center justify-between mb-1">
                            <label for="tipo_equipamento_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide">
                                Tipo de Equipamento *
                            </label>
                            <span id="loadingTipo" class="text-[11px] text-slate-500 hidden animate-pulse">Carregando...</span>
                        </div>
                        <select name="tipo_equipamento_id" id="tipo_equipamento_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Selecione o Tipo...</option>
                        </select>
                        @error('tipo_equipamento_id')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="fieldModelo" class="hidden">
                        <div class="flex items-center justify-between mb-1">
                            <label for="modelo_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide">
                                Modelo *
                            </label>
                            <span id="loadingModelo" class="text-[11px] text-slate-500 hidden animate-pulse">Carregando...</span>
                        </div>
                        <select name="modelo_id" id="modelo_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Selecione o Modelo...</option>
                        </select>
                        @error('modelo_id')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div id="fieldAplicacao" class="hidden">
                        <div class="flex items-center justify-between mb-1">
                            <label for="aplicacao_id" class="block text-xs font-bold text-gray-700 uppercase tracking-wide">
                                Aplicação *
                            </label>
                            <span id="loadingAplicacao" class="text-[11px] text-slate-500 hidden animate-pulse">Carregando...</span>
                        </div>
                        <select name="aplicacao_id" id="aplicacao_id" class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                            <option value="">Selecione a Aplicação...</option>
                        </select>
                        @error('aplicacao_id')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                </div>
            </div>

            <!-- SEÇÃO 3: ARQUIVO -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-[#13284c] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 bg-slate-100 text-[#13284c] rounded-lg text-xs font-bold flex items-center justify-center">3</span>
                    Arquivo
                </h2>

                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                    Arquivo do documento * (Apenas PDF - Máx. 50 MB)
                </label>

                <div id="dropZone" class="border-2 border-dashed border-slate-300 hover:border-[#13284c] rounded-2xl p-6 text-center cursor-pointer bg-slate-50 hover:bg-slate-100 transition relative">
                    <input type="file" name="arquivo" id="arquivo" accept="application/pdf" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    
                    <div id="fileUploadPlaceholder" class="flex flex-col items-center justify-center space-y-2">
                        <div class="w-12 h-12 bg-white rounded-full border border-slate-200 flex items-center justify-center shadow-sm text-[#13284c]">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        </div>
                        <p class="text-xs font-semibold text-gray-700">
                            <span class="text-[#13284c] font-bold">Clique para selecionar</span> ou arraste o arquivo PDF
                        </p>
                        <p class="text-[11px] text-gray-400">Formato aceito: .pdf (Até 50 MB)</p>
                    </div>

                    <div id="filePreview" class="hidden flex items-center justify-between bg-white p-4 rounded-xl border border-slate-300 shadow-sm text-left">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 bg-red-50 text-red-600 rounded-lg flex items-center justify-center font-bold text-lg shrink-0">
                                📄
                            </div>
                            <div>
                                <div id="fileNameDisplay" class="text-sm font-bold text-gray-800 break-all"></div>
                                <div id="fileSizeDisplay" class="text-xs text-gray-500 font-mono"></div>
                            </div>
                        </div>
                        <span class="text-xs font-semibold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                            Pronto para envio
                        </span>
                    </div>
                </div>
                @error('arquivo')
                    <p class="text-xs text-red-600 mt-2 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- SEÇÃO 4: VERSÃO -->
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200">
                <h2 class="text-lg font-bold text-[#13284c] mb-4 pb-2 border-b border-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 bg-slate-100 text-[#13284c] rounded-lg text-xs font-bold flex items-center justify-center">4</span>
                    Versão
                </h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="versao" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Versão *
                        </label>
                        <input type="text" name="versao" id="versao" value="{{ old('versao', '1.0') }}" required placeholder="Ex: 1.0 ou 2.1" class="w-full bg-slate-50 border @error('versao') border-red-500 @else border-slate-300 @enderror rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                        @error('versao')
                            <p class="text-xs text-red-600 mt-1 font-medium">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="data_documento" class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-1">
                            Data do Documento
                        </label>
                        <input type="date" name="data_documento" id="data_documento" value="{{ old('data_documento') }}" class="w-full bg-slate-50 border border-slate-300 rounded-xl py-2.5 px-3.5 text-sm focus:outline-none focus:ring-2 focus:ring-[#13284c]">
                    </div>
                </div>
            </div>

            <!-- BOTÕES -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <a href="{{ route('documentos.index') }}" class="px-5 py-2.5 rounded-xl text-xs font-bold text-gray-600 hover:text-gray-900 bg-slate-200 hover:bg-slate-300 transition">
                    Cancelar
                </a>
                <button type="submit" id="btnSubmit" class="bg-[#13284c] hover:bg-[#0c1b35] text-white px-6 py-2.5 rounded-xl text-xs font-bold shadow-md transition active:scale-95 flex items-center gap-2">
                    <span id="btnSubmitText">Cadastrar documento</span>
                    <span id="btnSubmitSpinner" class="hidden animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                </button>
            </div>

        </form>

    </div>

    <!-- SCRIPT DE INTERAÇÃO E CARREGAMENTO AJAX -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('formDocumento');
            const btnSubmit = document.getElementById('btnSubmit');
            const btnSubmitText = document.getElementById('btnSubmitText');
            const btnSubmitSpinner = document.getElementById('btnSubmitSpinner');

            form.addEventListener('submit', function() {
                btnSubmit.disabled = true;
                btnSubmitText.textContent = 'Enviando...';
                btnSubmitSpinner.classList.remove('hidden');
            });

            // UPLOAD E PREVIEW DE ARQUIVO
            const fileInput = document.getElementById('arquivo');
            const fileUploadPlaceholder = document.getElementById('fileUploadPlaceholder');
            const filePreview = document.getElementById('filePreview');
            const fileNameDisplay = document.getElementById('fileNameDisplay');
            const fileSizeDisplay = document.getElementById('fileSizeDisplay');
            const dropZone = document.getElementById('dropZone');

            function formatBytes(bytes) {
                if (bytes === 0) return '0 Bytes';
                const k = 1024;
                const sizes = ['Bytes', 'KB', 'MB', 'GB'];
                const i = Math.floor(Math.log(bytes) / Math.log(k));
                return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
            }

            fileInput.addEventListener('change', function() {
                if (this.files && this.files[0]) {
                    const file = this.files[0];
                    if (file.type !== 'application/pdf') {
                        alert('Apenas arquivos em formato PDF são permitidos.');
                        this.value = '';
                        filePreview.classList.add('hidden');
                        fileUploadPlaceholder.classList.remove('hidden');
                        return;
                    }

                    fileNameDisplay.textContent = file.name;
                    fileSizeDisplay.textContent = formatBytes(file.size);
                    fileUploadPlaceholder.classList.add('hidden');
                    filePreview.classList.remove('hidden');
                }
            });

            // CAMPOS DINÂMICOS
            const radioRelacionamento = document.querySelectorAll('input[name="tipo_relacionamento"]');
            const dynamicContainer = document.getElementById('dynamicFieldsContainer');
            const fieldFabricante = document.getElementById('fieldFabricante');
            const fieldTipoEquipamento = document.getElementById('fieldTipoEquipamento');
            const fieldModelo = document.getElementById('fieldModelo');
            const fieldAplicacao = document.getElementById('fieldAplicacao');

            const selectFabricante = document.getElementById('fabricante_id');
            const selectTipoEquipamento = document.getElementById('tipo_equipamento_id');
            const selectModelo = document.getElementById('modelo_id');
            const selectAplicacao = document.getElementById('aplicacao_id');

            const loadingTipo = document.getElementById('loadingTipo');
            const loadingModelo = document.getElementById('loadingModelo');
            const loadingAplicacao = document.getElementById('loadingAplicacao');

            const oldValues = {
                fabricante_id: "{{ old('fabricante_id') }}",
                tipo_equipamento_id: "{{ old('tipo_equipamento_id') }}",
                modelo_id: "{{ old('modelo_id') }}",
                aplicacao_id: "{{ old('aplicacao_id') }}"
            };

            function handleRelacionamentoChange(val) {
                if (val === 'geral') {
                    dynamicContainer.classList.add('hidden');
                    fieldFabricante.classList.add('hidden');
                    fieldTipoEquipamento.classList.add('hidden');
                    fieldModelo.classList.add('hidden');
                    fieldAplicacao.classList.add('hidden');
                } else {
                    dynamicContainer.classList.remove('hidden');

                    if (val === 'fabricante') {
                        fieldFabricante.classList.remove('hidden');
                        fieldTipoEquipamento.classList.add('hidden');
                        fieldModelo.classList.add('hidden');
                        fieldAplicacao.classList.add('hidden');
                    } else if (val === 'modelo') {
                        fieldFabricante.classList.remove('hidden');
                        fieldTipoEquipamento.classList.remove('hidden');
                        fieldModelo.classList.remove('hidden');
                        fieldAplicacao.classList.add('hidden');
                    } else if (val === 'aplicacao') {
                        fieldFabricante.classList.remove('hidden');
                        fieldTipoEquipamento.classList.remove('hidden');
                        fieldModelo.classList.remove('hidden');
                        fieldAplicacao.classList.remove('hidden');
                    }
                }
            }

            radioRelacionamento.forEach(radio => {
                radio.addEventListener('change', (e) => handleRelacionamentoChange(e.target.value));
            });

            const selectedRadio = document.querySelector('input[name="tipo_relacionamento"]:checked');
            if (selectedRadio) handleRelacionamentoChange(selectedRadio.value);

            // CARREGAMENTO DEPENDENTE VIA FETCH
            async function loadTipos(fabricanteId, selectedValue = '') {
                if (!fabricanteId) return;
                loadingTipo.classList.remove('hidden');
                try {
                    const response = await fetch(`/api/tipos-equipamento?fabricante_id=${fabricanteId}`);
                    const data = await response.json();
                    selectTipoEquipamento.innerHTML = '<option value="">Selecione o Tipo...</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.nome;
                        if (selectedValue && item.id == selectedValue) opt.selected = true;
                        selectTipoEquipamento.appendChild(opt);
                    });
                } catch (e) {
                    console.error(e);
                } finally {
                    loadingTipo.classList.add('hidden');
                }
            }

            async function loadModelos(fabricanteId, tipoId, selectedValue = '') {
                if (!fabricanteId) return;
                loadingModelo.classList.remove('hidden');
                try {
                    let url = `/api/modelos?fabricante_id=${fabricanteId}`;
                    if (tipoId) url += `&tipo_equipamento_id=${tipoId}`;
                    const response = await fetch(url);
                    const data = await response.json();
                    selectModelo.innerHTML = '<option value="">Selecione o Modelo...</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.nome;
                        if (selectedValue && item.id == selectedValue) opt.selected = true;
                        selectModelo.appendChild(opt);
                    });
                } catch (e) {
                    console.error(e);
                } finally {
                    loadingModelo.classList.add('hidden');
                }
            }

            async function loadAplicacoes(modeloId, selectedValue = '') {
                if (!modeloId) return;
                loadingAplicacao.classList.remove('hidden');
                try {
                    const response = await fetch(`/api/aplicacoes?modelo_id=${modeloId}`);
                    const data = await response.json();
                    selectAplicacao.innerHTML = '<option value="">Selecione a Aplicação...</option>';
                    data.forEach(item => {
                        const opt = document.createElement('option');
                        opt.value = item.id;
                        opt.textContent = item.descricao;
                        if (selectedValue && item.id == selectedValue) opt.selected = true;
                        selectAplicacao.appendChild(opt);
                    });
                } catch (e) {
                    console.error(e);
                } finally {
                    loadingAplicacao.classList.add('hidden');
                }
            }

            selectFabricante.addEventListener('change', function() {
                loadTipos(this.value);
                loadModelos(this.value, selectTipoEquipamento.value);
            });

            selectTipoEquipamento.addEventListener('change', function() {
                loadModelos(selectFabricante.value, this.value);
            });

            selectModelo.addEventListener('change', function() {
                loadAplicacoes(this.value);
            });

            // RESTAURA SELEÇÕES ANTERIORES APÓS ERRO DE VALIDAÇÃO
            if (oldValues.fabricante_id) {
                loadTipos(oldValues.fabricante_id, oldValues.tipo_equipamento_id);
                loadModelos(oldValues.fabricante_id, oldValues.tipo_equipamento_id, oldValues.modelo_id);
                if (oldValues.modelo_id) loadAplicacoes(oldValues.modelo_id, oldValues.aplicacao_id);
            }
        });
    </script>

</body>
</html>