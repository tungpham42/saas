<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\Channel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ChannelService
{
    protected LLMService $llmService;

    public function __construct(LLMService $llmService)
    {
        $this->llmService = $llmService;
    }

    public function handleWebhook(Bot $bot, Channel $channel, array $payload): void
    {
        $method = match($channel->channel_type) {
            'fb' => $this->handleFacebook($bot, $channel, $payload),
            'zalo' => $this->handleZalo($bot, $channel, $payload),
            'tt' => $this->handleTikTok($bot, $channel, $payload),
            'sp' => $this->handleShopee($bot, $channel, $payload),
            'zlpn' => $this->handleZaloPersonal($bot, $channel, $payload),
            'wa' => $this->handleWhatsApp($bot, $channel, $payload),
            default => null,
        };
    }

    private function handleFacebook(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['entry'][0]['messaging'][0])) {
            $event = $payload['entry'][0]['messaging'][0];
            $senderId = $event['sender']['id'];

            if (isset($event['message']['text'])) {
                $message = $event['message']['text'];
                $sessionId = "fb__{$channel->id}__{$senderId}";

                $response = app(LLMService::class)->generateResponse($bot, $sessionId, $message);

                if (!empty($response['answer']) && !empty($config['fb_page_token'])) {
                    $this->sendFacebookMessage($config['fb_page_token'], $senderId, $response['answer']);
                }
            }
        }
    }

    private function sendFacebookMessage(string $token, string $recipientId, string $message): void
    {
        $message = $this->llmService->cleanTextForChannels($message);
        $url = "https://graph.facebook.com/v18.0/me/messages";

        Http::withHeaders(['Content-Type' => 'application/json'])
            ->post($url . "?access_token={$token}", [
                'recipient' => ['id' => $recipientId],
                'message' => ['text' => $message]
            ]);
    }

    private function handleZalo(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['event_name']) && $payload['event_name'] === 'user_send_text') {
            $senderId = $payload['sender']['id'];
            $message = $payload['message']['text'];
            $sessionId = "zalo__{$channel->id}__{$senderId}";

            $response = app(LLMService::class)->generateResponse($bot, $sessionId, $message);

            if (!empty($response['answer']) && !empty($config['zalo_access_token'])) {
                $this->sendZaloMessage($config['zalo_access_token'], $senderId, $response['answer']);
            }
        }
    }

    private function sendZaloMessage(string $token, string $recipientId, string $message): void
    {
        $message = $this->llmService->cleanTextForChannels($message);
        $url = "https://openapi.zalo.me/v3.0/oa/message/cs";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'access_token' => $token
        ])->post($url, [
            'recipient' => ['user_id' => $recipientId],
            'message' => ['text' => $message]
        ]);
    }

    private function handleTikTok(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['message']['text']) && isset($payload['sender']['id'])) {
            $senderId = $payload['sender']['id'];
            $message = $payload['message']['text'];
            $sessionId = "tt__{$channel->id}__{$senderId}";

            $response = app(LLMService::class)->generateResponse($bot, $sessionId, $message);

            if (!empty($response['answer']) && !empty($config['tiktok_access_token'])) {
                $this->sendTikTokMessage($config['tiktok_access_token'], $senderId, $response['answer']);
            }
        }
    }

    private function sendTikTokMessage(string $token, string $recipientId, string $message): void
    {
        $message = $this->llmService->cleanTextForChannels($message);
        $url = "https://open-api.tiktokglobalshop.com/customer_service/202309/conversations/{$recipientId}/messages";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'x-tts-access-token' => $token
        ])->post($url, [
            'content' => ['text' => $message],
            'type' => 'TEXT'
        ]);
    }

    private function handleShopee(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['message']) && isset($payload['from_id'])) {
            $senderId = $payload['from_id'];
            $message = $payload['message']['text'] ?? '';
            $sessionId = "sp__{$channel->id}__{$senderId}";

            if (!empty($message)) {
                $response = app(LLMService::class)->generateResponse($bot, $sessionId, $message);

                if (!empty($response['answer']) && !empty($config['shopee_access_token']) && !empty($config['shopee_shop_id'])) {
                    $this->sendShopeeMessage($config['shopee_access_token'], $config['shopee_shop_id'], $senderId, $response['answer']);
                }
            }
        }
    }

    private function sendShopeeMessage(string $token, string $shopId, string $recipientId, string $message): void
    {
        $message = $this->llmService->cleanTextForChannels($message);
        $url = "https://partner.shopeemobile.com/api/v2/sellerchat/send_message";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post($url, [
            'to_id' => (int) $recipientId,
            'message_type' => 'text',
            'content' => ['text' => $message]
        ]);
    }

    private function handleZaloPersonal(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['sender_id']) && isset($payload['message'])) {
            $senderId = $payload['sender_id'];
            $message = $payload['message'];
            $sessionId = "zlpn__{$channel->id}__{$senderId}";

            $response = app(LLMService::class)->generateResponse($bot, $sessionId, $message);

            if (!empty($response['answer']) && !empty($config['zalo_personal_token'])) {
                $this->sendZaloPersonalMessage($config['zalo_personal_token'], $senderId, $response['answer']);
            }
        }
    }

    private function sendZaloPersonalMessage(string $token, string $recipientId, string $message): void
    {
        $message = $this->llmService->cleanTextForChannels($message);
        $parts = explode('|', $token);
        $apiUrl = $parts[0] ?? '';
        $apiToken = $parts[1] ?? $token;

        if (filter_var($apiUrl, FILTER_VALIDATE_URL)) {
            Http::withHeaders([
                'Content-Type' => 'application/json',
                'Authorization' => 'Bearer ' . $apiToken
            ])->post($apiUrl, [
                'to' => $recipientId,
                'message' => $message
            ]);
        }
    }

    private function handleWhatsApp(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            $messageData = $payload['entry'][0]['changes'][0]['value']['messages'][0];
            $senderId = $messageData['from'];

            if (isset($messageData['text']['body'])) {
                $message = $messageData['text']['body'];
                $sessionId = "wa__{$channel->id}__{$senderId}";

                $response = app(LLMService::class)->generateResponse($bot, $sessionId, $message);

                if (!empty($response['answer']) && !empty($config['whatsapp_token']) && !empty($config['whatsapp_phone_number_id'])) {
                    $this->sendWhatsAppMessage($config['whatsapp_token'], $config['whatsapp_phone_number_id'], $senderId, $response['answer']);
                }
            }
        }
    }

    private function sendWhatsAppMessage(string $token, string $phoneNumberId, string $recipientId, string $message): void
    {
        $message = $this->llmService->cleanTextForChannels($message);
        $url = "https://graph.facebook.com/v18.0/{$phoneNumberId}/messages";

        Http::withHeaders([
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer ' . $token
        ])->post($url, [
            'messaging_product' => 'whatsapp',
            'to' => $recipientId,
            'type' => 'text',
            'text' => ['body' => $message]
        ]);
    }

    public function verifyWebhook(Channel $channel, array $query): ?string
    {
        $config = $channel->config;

        if ($channel->channel_type === 'fb') {
            $verifyToken = $config['fb_verify_token'] ?? '';
            if (($query['hub_mode'] ?? '') === 'subscribe' && ($query['hub_verify_token'] ?? '') === $verifyToken) {
                return $query['hub_challenge'] ?? null;
            }
        } elseif ($channel->channel_type === 'wa') {
            $verifyToken = $config['whatsapp_verify_token'] ?? '';
            if (($query['hub_mode'] ?? '') === 'subscribe' && ($query['hub_verify_token'] ?? '') === $verifyToken) {
                return $query['hub_challenge'] ?? null;
            }
        }

        return null;
    }
}
