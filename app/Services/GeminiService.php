<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Exception;

class GeminiService
{
    protected string $apiKey;
    protected string $model;

    public function __construct()
    {
        $this->apiKey = (string) config('services.gemini.key');
        $this->model  = (string) config('services.gemini.model', 'gemini-3.8-flash');
    }

    /**
     * Envia solicitação à API do Google Gemini.
     */
    public function perguntar(?string $caminhoRelativo, string $pergunta): string
    {
        if (empty($this->apiKey)) {
            throw new Exception('Chave GEMINI_API_KEY não configurada. Verifique seu arquivo .env e execute "php artisan config:clear".');
        }

        $parts = [];

        // 1. Processa documento se fornecido usando a facade Storage
        if (!empty($caminhoRelativo)) {
            $disk = Storage::disk('local');

            if (!$disk->exists($caminhoRelativo)) {
                throw new Exception("O arquivo anexado não foi localizado no storage: {$caminhoRelativo}");
            }

            $fileContent = $disk->get($caminhoRelativo);

            if (strlen($fileContent) > 10 * 1024 * 1024) {
                throw new Exception("O arquivo selecionado excede o limite de 10MB para análise rápida.");
            }

            $mimeType = $disk->mimeType($caminhoRelativo) ?: 'application/pdf';

            $parts[] = [
                'inline_data' => [
                    'mime_type' => $mimeType,
                    'data'      => base64_encode($fileContent)
                ]
            ];
        }

        // 2. Adiciona o prompt de texto
        $parts[] = [
            'text' => "Você é o assistente virtual especialista do sistema FersaHUB. Responda de forma direta, objetiva e profissional: {$pergunta}"
        ];

        // 3. Formata o modelo garantindo gemini-3.8-flash
        $modeloLimpo = str_starts_with($this->model, 'models/') 
            ? substr($this->model, 7) 
            : $this->model;

        $url = "https://generativelanguage.googleapis.com/v1beta/models/{$modeloLimpo}:generateContent";

        // 4. Executa a requisição HTTP com proteções contra congelamento no Windows
        try {
            $options = [
                'connect_timeout'  => 10,
                'force_ip_resolve' => 'v4', // Força resolução IPv4 no Guzzle
                'version'          => 1.1,  // Impede deadlock do HTTP/2 no cURL do Windows
            ];

            // Em ambiente local, desativa verificação SSL para evitar travamento em OCSP/CRL
            if (app()->environment('local')) {
                $options['verify'] = false;
            }

            $response = Http::timeout(60)
                ->withOptions($options)
                ->withHeaders([
                    'Content-Type'   => 'application/json',
                    'x-goog-api-key' => $this->apiKey,
                    'Expect'         => '', // IMPORTANTE: Impede o cURL de congelar aguardando 100-continue
                ])->post($url, [
                    'contents' => [
                        ['parts' => $parts]
                    ],
                    'generationConfig' => [
                        'temperature' => 0.3,
                    ]
                ]);

        } catch (\Throwable $e) {
            Log::error("Erro de Conexão Gemini: " . $e->getMessage());
            throw new Exception("Falha de conexão com os servidores do Google: " . $e->getMessage());
        }

        // 5. Trata respostas de erro da API
        if (!$response->successful()) {
            $status  = $response->status();
            $jsonErr = $response->json();
            $erroMsg = $jsonErr['error']['message'] ?? $response->body();

            Log::error("Gemini API Error [Modelo: {$modeloLimpo} | Status HTTP {$status}]: {$erroMsg}");

            throw new Exception("Erro na API Gemini (HTTP {$status}): {$erroMsg}");
        }

        $json = $response->json();

        // 6. Trata bloqueios de segurança
        if (isset($json['promptFeedback']['blockReason'])) {
            $reason = $json['promptFeedback']['blockReason'];
            Log::warning("Gemini Prompt Bloqueado: {$reason}");
            throw new Exception("A requisição foi bloqueada pelos filtros de segurança do Google. Motivo: {$reason}");
        }

        $candidate = $json['candidates'][0] ?? null;
        if (!$candidate) {
            throw new Exception("A IA não retornou uma resposta válida.");
        }

        // 7. Trata interrupções
        $finishReason = $candidate['finishReason'] ?? null;
        if ($finishReason && !in_array($finishReason, ['STOP', 'MAX_TOKENS'])) {
            Log::warning("Gemini FinishReason Anormal: {$finishReason}");
            throw new Exception("A geração de texto foi interrompida pela IA. Motivo: {$finishReason}");
        }

        // 8. Extrai o texto final
        $partesTexto = $candidate['content']['parts'] ?? [];
        $textoFinal = '';

        foreach ($partesTexto as $p) {
            if (isset($p['text']) && is_string($p['text'])) {
                $textoFinal .= $p['text'];
            }
        }

        if (trim($textoFinal) === '') {
            throw new Exception("A IA respondeu com sucesso, mas o conteúdo veio vazio.");
        }

        return $textoFinal;
    }
}