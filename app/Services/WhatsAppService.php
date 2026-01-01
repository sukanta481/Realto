<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    protected $apiUrl;
    protected $accessToken;
    protected $phoneNumberId;

    public function __construct()
    {
        $this->apiUrl = config('services.whatsapp.api_url', 'https://graph.facebook.com/v18.0');
        $this->accessToken = config('services.whatsapp.access_token');
        $this->phoneNumberId = config('services.whatsapp.phone_number_id');
    }

    /**
     * Check if WhatsApp is configured.
     */
    public function isConfigured(): bool
    {
        return !empty($this->accessToken) && !empty($this->phoneNumberId);
    }

    /**
     * Send a text message.
     */
    public function sendMessage(string $to, string $message): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp not configured'];
        }

        // Format phone number (remove + and spaces)
        $to = preg_replace('/[^0-9]/', '', $to);
        
        // Add country code if not present (assuming India)
        if (strlen($to) === 10) {
            $to = '91' . $to;
        }

        try {
            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", [
                    'messaging_product' => 'whatsapp',
                    'to' => $to,
                    'type' => 'text',
                    'text' => [
                        'body' => $message,
                    ],
                ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error.message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp send failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Send a template message (pre-approved by Meta).
     */
    public function sendTemplate(string $to, string $templateName, array $components = []): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'error' => 'WhatsApp not configured'];
        }

        $to = preg_replace('/[^0-9]/', '', $to);
        if (strlen($to) === 10) {
            $to = '91' . $to;
        }

        try {
            $payload = [
                'messaging_product' => 'whatsapp',
                'to' => $to,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => 'en',
                    ],
                ],
            ];

            if (!empty($components)) {
                $payload['template']['components'] = $components;
            }

            $response = Http::withToken($this->accessToken)
                ->post("{$this->apiUrl}/{$this->phoneNumberId}/messages", $payload);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message_id' => $response->json('messages.0.id'),
                ];
            }

            return [
                'success' => false,
                'error' => $response->json('error.message', 'Unknown error'),
            ];
        } catch (\Exception $e) {
            Log::error('WhatsApp template failed: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Generate WhatsApp click-to-chat URL.
     */
    public static function generateChatUrl(string $phone, string $message = ''): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (strlen($phone) === 10) {
            $phone = '91' . $phone;
        }
        
        $url = "https://wa.me/{$phone}";
        if (!empty($message)) {
            $url .= '?text=' . urlencode($message);
        }
        
        return $url;
    }

    /**
     * Pre-built message templates.
     */
    public static function getMessageTemplate(string $type, array $data = []): string
    {
        $templates = [
            'welcome' => "Hi {name}! 👋\n\nThank you for your interest in our properties. I'm {agent_name} from {company_name}.\n\nHow can I help you today?",
            
            'property_details' => "🏠 *Property Details*\n\n📍 {title}\n💰 {price}\n📏 {area}\n🛏️ {bedrooms} BHK\n\n{address}\n\nWould you like to schedule a site visit?",
            
            'site_visit' => "Hi {name}! 👋\n\nThis is a reminder for your site visit:\n\n📅 Date: {date}\n⏰ Time: {time}\n📍 Location: {location}\n\nPlease confirm your attendance.\n\nThank you!",
            
            'follow_up' => "Hi {name}! 👋\n\nJust following up on our recent conversation about {property}.\n\nDo you have any questions or would you like to proceed further?\n\nLooking forward to hearing from you!",
        ];

        $message = $templates[$type] ?? $templates['welcome'];
        
        foreach ($data as $key => $value) {
            $message = str_replace('{' . $key . '}', $value, $message);
        }
        
        return $message;
    }
}
