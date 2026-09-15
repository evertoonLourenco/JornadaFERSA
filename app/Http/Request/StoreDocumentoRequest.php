<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Modelo;
use App\Models\Aplicacao;

class StoreDocumentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'categoria_id' => ['required', 'exists:categorias_documento,id'],
            'descricao' => ['nullable', 'string', 'max:2000'],
            'observacao' => ['nullable', 'string', 'max:2000'],
            'tipo_relacionamento' => ['required', 'in:geral,fabricante,modelo,aplicacao'],

            // Validações condicionais
            'fabricante_id' => [
                'required_if:tipo_relacionamento,fabricante,modelo,aplicacao',
                'nullable',
                'exists:fabricantes,id'
            ],
            'tipo_equipamento_id' => [
                'required_if:tipo_relacionamento,modelo,aplicacao',
                'nullable',
                'exists:tipos_equipamento,id'
            ],
            'modelo_id' => [
                'required_if:tipo_relacionamento,modelo,aplicacao',
                'nullable',
                'exists:modelos,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->fabricante_id) {
                        $query = Modelo::where('id', $value)
                            ->where('fabricante_id', $this->fabricante_id);

                        if ($this->tipo_equipamento_id) {
                            $query->where('tipo_equipamento_id', $this->tipo_equipamento_id);
                        }

                        if (!$query->exists()) {
                            $fail('O modelo selecionado não pertence ao fabricante ou tipo selecionado.');
                        }
                    }
                }
            ],
            'aplicacao_id' => [
                'required_if:tipo_relacionamento,aplicacao',
                'nullable',
                'exists:aplicacoes,id',
                function ($attribute, $value, $fail) {
                    if ($value && $this->modelo_id) {
                        $exists = Aplicacao::where('id', $value)
                            ->where('modelo_id', $this->modelo_id)
                            ->exists();
                        if (!$exists) {
                            $fail('A aplicação selecionada não pertence ao modelo informado.');
                        }
                    }
                }
            ],

            'arquivo' => ['required', 'file', 'mimes:pdf', 'max:51200'], // Máximo 50MB (51200 KB)
            'versao' => ['required', 'string', 'max:50'],
            'data_documento' => ['nullable', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'titulo.required' => 'Informe o título do documento.',
            'categoria_id.required' => 'Selecione uma categoria para o documento.',
            'categoria_id.exists' => 'A categoria selecionada é inválida.',
            'tipo_relacionamento.required' => 'Escolha o tipo de relacionamento do documento.',
            'fabricante_id.required_if' => 'Selecione o fabricante correspondente.',
            'tipo_equipamento_id.required_if' => 'Selecione o tipo de equipamento.',
            'modelo_id.required_if' => 'Selecione o modelo do equipamento.',
            'aplicacao_id.required_if' => 'Selecione a aplicação do documento.',
            'arquivo.required' => 'Selecione um arquivo PDF para envio.',
            'arquivo.mimes' => 'O arquivo enviado deve estar no formato PDF.',
            'arquivo.max' => 'O tamanho do arquivo não pode exceder 50 MB.',
            'versao.required' => 'Informe a versão inicial do documento.',
        ];
    }
}