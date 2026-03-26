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

    public function handleFacebook(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['entry'][0]['messaging'][0])) {
            $event = $payload['entry'][0]['messaging'][0];
            $senderId = $event['sender']['id'];

            if (isset($event['message']['text'])) {
                $message = $event['message']['text'];
                $sessionId = "fb__{$channel->id}__{$senderId}";

                $response = $this->llmService->generateResponse($bot, $sessionId, $message);

                if (!empty($response['answer']) && !empty($config['fb_page_token'])) {
                    $this->sendFacebookMessage($config['fb_page_token'], $senderId, $response['answer']);
                }
            }
        }
    }

    public function handleZalo(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['event_name']) && $payload['event_name'] === 'user_send_text') {
            $senderId = $payload['sender']['id'];
            $message = $payload['message']['text'];
            $sessionId = "zalo__{$channel->id}__{$senderId}";

            $response = $this->llmService->generateResponse($bot, $sessionId, $message);

            if (!empty($response['answer']) && !empty($config['zalo_access_token'])) {
                $this->sendZaloMessage($config['zalo_access_token'], $senderId, $response['answer']);
            }
        }
    }

    public function handleTikTok(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['message']['text']) && isset($payload['sender']['id'])) {
            $senderId = $payload['sender']['id'];
            $message = $payload['message']['text'];
            $sessionId = "tt__{$channel->id}__{$senderId}";

            $response = $this->llmService->generateResponse($bot, $sessionId, $message);

            if (!empty($response['answer']) && !empty($config['tiktok_access_token'])) {
                $this->sendTikTokMessage($config['tiktok_access_token'], $senderId, $response['answer']);
            }
        }
    }

    public function handleShopee(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['message']) && isset($payload['from_id'])) {
            $senderId = $payload['from_id'];
            $message = $payload['message']['text'] ?? '';
            $sessionId = "sp__{$channel->id}__{$senderId}";

            if (!empty($message)) {
                $response = $this->llmService->generateResponse($bot, $sessionId, $message);

                if (!empty($response['answer']) && !empty($config['shopee_access_token']) && !empty($config['shopee_shop_id'])) {
                    $this->sendShopeeMessage($config['shopee_access_token'], $config['shopee_shop_id'], $senderId, $response['answer']);
                }
            }
        }
    }

    public function handleZaloPersonal(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['sender_id']) && isset($payload['message'])) {
            $senderId = $payload['sender_id'];
            $message = $payload['message'];
            $sessionId = "zlpn__{$channel->id}__{$senderId}";

            $response = $this->llmService->generateResponse($bot, $sessionId, $message);

            if (!empty($response['answer']) && !empty($config['zalo_personal_token'])) {
                $this->sendZaloPersonalMessage($config['zalo_personal_token'], $senderId, $response['answer']);
            }
        }
    }

    public function handleWhatsApp(Bot $bot, Channel $channel, array $payload): void
    {
        $config = $channel->config;

        if (isset($payload['entry'][0]['changes'][0]['value']['messages'][0])) {
            $messageData = $payload['entry'][0]['changes'][0]['value']['messages'][0];
            $senderId = $messageData['from'];

            if (isset($messageData['text']['body'])) {
                $message = $messageData['text']['body'];
                $sessionId = "wa__{$channel->id}__{$senderId}";

                $response = $this->llmService->generateResponse($bot, $sessionId, $message);

                if (!empty($response['answer']) && !empty($config['whatsapp_token']) && !empty($config['whatsapp_phone_number_id'])) {
                    $this->sendWhatsAppMessage($config['whatsapp_token'], $config['whatsapp_phone_number_id'], $senderId, $response['answer']);
                }
            }
        }
    }

    // Message sending methods
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
}
