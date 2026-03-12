<?php

namespace App\Services;

use Twilio\Rest\Client;
use Illuminate\Support\Facades\Log;

class TwilioService
{
    protected Client $client;
    protected string $from;

    public function __construct()
{
    $sid   = config('services.twilio.sid')   ?? env('TWILIO_SID');
    $token = config('services.twilio.token') ?? env('TWILIO_TOKEN');
    $from  = config('services.twilio.whatsapp_from') ?? env('TWILIO_WHATSAPP_FROM');

    $this->client = new Client($sid, $token);
    $this->from   = $from ?? 'whatsapp:+14155238886';
}

    /*──────────────────────────────────────
     | Enviar mensagem WhatsApp
    ──────────────────────────────────────*/
    public function enviar(string $para, string $mensagem): bool
    {
        try {
            $this->client->messages->create(
                'whatsapp:' . $para,
                [
                    'from' => $this->from,
                    'body' => $mensagem,
                ]
            );
            return true;
        } catch (\Exception $e) {
            Log::error('Twilio WhatsApp erro: ' . $e->getMessage(), [
                'para'     => $para,
                'mensagem' => $mensagem,
            ]);
            return false;
        }
    }

    /*──────────────────────────────────────
     | Enviar para múltiplos números
    ──────────────────────────────────────*/
    public function enviarParaVarios(array $numeros, string $mensagem): void
    {
        foreach ($numeros as $numero) {
            $this->enviar($numero, $mensagem);
        }
    }

    /*──────────────────────────────────────
     | ALERTA — Stock Crítico
    ──────────────────────────────────────*/
    public function alertaStockCritico(string $produto, float $quantidade, string $unidade = ''): string
    {
        return implode("\n", [
            '⚠️ *ALERTA DE STOCK CRÍTICO*',
            '━━━━━━━━━━━━━━━━━━━━',
            "📦 *Produto:* {$produto}",
            "📉 *Stock actual:* {$quantidade} {$unidade}",
            '━━━━━━━━━━━━━━━━━━━━',
            '_Sistema de Gestão Industrial — BYMOZE_',
        ]);
    }

    /*──────────────────────────────────────
     | ALERTA — Máquina Avariada
    ──────────────────────────────────────*/
    public function alertaMaquinaAvariada(string $maquina, string $descricao, string $responsavel = ''): string
    {
        $linhas = [
            '🔴 *AVARIA REGISTADA*',
            '━━━━━━━━━━━━━━━━━━━━',
            "⚙️ *Máquina:* {$maquina}",
            "📝 *Descrição:* {$descricao}",
        ];
        if ($responsavel) {
            $linhas[] = "👤 *Registado por:* {$responsavel}";
        }
        $linhas[] = '🕐 *Data/Hora:* ' . now()->format('d/m/Y H:i');
        $linhas[] = '━━━━━━━━━━━━━━━━━━━━';
        $linhas[] = '_Sistema de Gestão Industrial — BYMOZE_';

        return implode("\n", $linhas);
    }

    /*──────────────────────────────────────
     | ALERTA — Nova Requisição
    ──────────────────────────────────────*/
    public function alertaNovaRequisicao(string $numero, string $solicitante, string $items, float $total = 0): string
    {
        $linhas = [
            '🛒 *NOVA REQUISIÇÃO PENDENTE*',
            '━━━━━━━━━━━━━━━━━━━━',
            "📋 *Nº Requisição:* {$numero}",
            "👤 *Solicitante:* {$solicitante}",
            "📦 *Items:* {$items}",
        ];
        if ($total > 0) {
            $linhas[] = '💰 *Total estimado:* ' . number_format($total, 2, ',', '.') . ' MT';
        }
        $linhas[] = '🕐 *Data:* ' . now()->format('d/m/Y H:i');
        $linhas[] = '━━━━━━━━━━━━━━━━━━━━';
        $linhas[] = '👉 Aceda ao sistema para aprovar.';
        $linhas[] = '_Sistema de Gestão Industrial — BYMOZE_';

        return implode("\n", $linhas);
    }

    
}