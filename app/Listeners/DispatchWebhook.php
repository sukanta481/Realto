<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class DispatchWebhook
{
    /**
     * Handle the event.
     */
    public function handle(object $event): void
    {
        // Get webhook URL from environment or config
        $webhookUrl = config('services.webhook.url') ?? env('WEBHOOK_URL');
        
        if (!$webhookUrl) {
            return;
        }
        
        // Get webhook payload
        $payload = method_exists($event, 'toWebhook') 
            ? $event->toWebhook() 
            : ['event' => get_class($event), 'data' => []];
        
        // Add authentication secret if configured
        $secret = config('services.webhook.secret') ?? env('WEBHOOK_SECRET');
        if ($secret) {
            $payload['signature'] = hash_hmac('sha256', json_encode($payload['data']), $secret);
        }
        
        try {
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-Webhook-Source' => 'realto-crm',
                    'X-Webhook-Event' => $payload['event'] ?? 'unknown',
                ])
                ->post($webhookUrl, $payload);
            
            if ($response->failed()) {
                Log::warning('Webhook delivery failed', [
                    'url' => $webhookUrl,
                    'event' => $payload['event'] ?? 'unknown',
                    'status' => $response->status(),
                    'body' => $response->body()
                ]);
            } else {
                Log::info('Webhook delivered successfully', [
                    'event' => $payload['event'] ?? 'unknown',
                    'status' => $response->status()
                ]);
            }
        } catch (\Exception $e) {
            Log::error('Webhook delivery error', [
                'url' => $webhookUrl,
                'event' => $payload['event'] ?? 'unknown',
                'error' => $e->getMessage()
            ]);
        }
    }
}
