<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\ChatLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LLMService
{
    public function generateResponse(Bot $bot, string $sessionId, string $message): array
    {
        // Save user message
        $userMsg = $bot->chatLogs()->create([
            'session_id' => $sessionId,
            'role' => 'user',
            'content' => $message
        ]);

        // Check for admin timeout
        if ($this->hasRecentAdminMessage($bot, $sessionId)) {
            return [
                'answer' => '',
                'bot_msg_id' => null,
                'has_admin' => true
            ];
        }

        // Get RAG context
        $ragContext = $this->getRAGContext($bot, $message);

        // Get chat history
        $history = $this->getChatHistory($bot, $sessionId, $userMsg->id);

        // Build system prompt
        $systemPrompt = $this->buildSystemPrompt($bot, $ragContext, $history);

        // Call LLM API
        $answer = $this->callLLM($bot, $systemPrompt, $message);

        // Save bot response
        $botMsg = $bot->chatLogs()->create([
            'session_id' => $sessionId,
            'role' => 'bot',
            'content' => $answer
        ]);

        return [
            'answer' => $answer,
            'bot_msg_id' => $botMsg->id,
            'has_admin' => false
        ];
    }

    private function hasRecentAdminMessage(Bot $bot, string $sessionId): bool
    {
        $timeoutMins = $bot->admin_timeout_mins ?? 15;

        return $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->where('role', 'admin')
            ->where('created_at', '>=', now()->subMinutes($timeoutMins))
            ->exists();
    }

    private function getRAGContext(Bot $bot, string $message): string
    {
        $terms = array_filter(explode(' ', mb_strtolower($message)), function($term) {
            return mb_strlen($term) > 2;
        });

        if (empty($terms)) {
            return '';
        }

        $context = '';
        $docs = $bot->ragDocuments()
            ->where(function($query) use ($terms) {
                foreach ($terms as $term) {
                    $query->orWhere('content', 'LIKE', '%' . addcslashes($term, '%_') . '%');
                }
            })
            ->limit(2)
            ->get();

        foreach ($docs as $doc) {
            $context .= "Source: {$doc->title}\n";
            $context .= "Content: ... " . mb_substr($doc->content, 0, 1500) . " ...\n\n";
        }

        return $context;
    }

    private function getChatHistory(Bot $bot, string $sessionId, int $currentMsgId): string
    {
        $limit = $bot->history_limit ?? 5;

        if ($limit <= 0) {
            return '';
        }

        $pastMessages = $bot->chatLogs()
            ->where('session_id', $sessionId)
            ->where('id', '<', $currentMsgId)
            ->orderBy('id', 'desc')
            ->limit($limit)
            ->get()
            ->reverse();

        $history = '';
        foreach ($pastMessages as $msg) {
            $roleLabel = $msg->role === 'user' ? 'User' : 'AI/Admin';
            $history .= "{$roleLabel}: " . $msg->content . "\n";
        }

        return $history;
    }

    private function buildSystemPrompt(Bot $bot, string $ragContext, string $history): string
    {
        return implode("\n\n", array_filter([
            $bot->prompt_persona,
            "TASK:\n{$bot->prompt_task}",
            "FORMAT:\n{$bot->prompt_format}",
            "CONTEXT:\n{$bot->prompt_context}",
            "KNOWLEDGE BASE:\n" . ($ragContext ?: "No specific knowledge base context found."),
            "RECENT CHAT HISTORY:\n" . ($history ?: "This is the start of the conversation.")
        ]));
    }

    private function callLLM(Bot $bot, string $systemPrompt, string $userMessage): string
    {
        if (empty($bot->provider_api_key)) {
            return "API key not configured for this bot.";
        }

        $provider = $bot->provider;
        $temperature = $bot->temperature ?? 0.5;
        $maxTokens = $bot->max_tokens ?? 1024;

        try {
            if ($provider === 'openai') {
                return $this->callOpenAI($bot, $systemPrompt, $userMessage, $temperature, $maxTokens);
            } elseif ($provider === 'groq') {
                return $this->callGroq($bot, $systemPrompt, $userMessage, $temperature, $maxTokens);
            } elseif ($provider === 'gemini') {
                return $this->callGemini($bot, $systemPrompt, $userMessage, $temperature, $maxTokens);
            }

            return "Unknown provider: {$provider}";
        } catch (\Exception $e) {
            Log::error('LLM API Error', ['error' => $e->getMessage(), 'bot_id' => $bot->id]);
            return "AI Error: Could not generate response. Please try again later.";
        }
    }

    private function callOpenAI(Bot $bot, string $systemPrompt, string $userMessage, float $temperature, int $maxTokens): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot->provider_api_key,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.openai.com/v1/chat/completions', [
            'model' => $bot->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ]);

        if ($response->failed()) {
            throw new \Exception('OpenAI API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? 'No response from AI.';
    }

    private function callGroq(Bot $bot, string $systemPrompt, string $userMessage, float $temperature, int $maxTokens): string
    {
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $bot->provider_api_key,
            'Content-Type' => 'application/json',
        ])->timeout(30)->post('https://api.groq.com/openai/v1/chat/completions', [
            'model' => $bot->model,
            'messages' => [
                ['role' => 'system', 'content' => $systemPrompt],
                ['role' => 'user', 'content' => $userMessage]
            ],
            'temperature' => $temperature,
            'max_tokens' => $maxTokens,
        ]);

        if ($response->failed()) {
            throw new \Exception('Groq API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['choices'][0]['message']['content'] ?? 'No response from AI.';
    }

    private function callGemini(Bot $bot, string $systemPrompt, string $userMessage, float $temperature, int $maxTokens): string
    {
        $response = Http::timeout(30)->post(
            "https://generativelanguage.googleapis.com/v1beta/models/{$bot->model}:generateContent",
            [
                'key' => $bot->provider_api_key
            ],
            [
                'system_instruction' => [
                    'parts' => [['text' => $systemPrompt]]
                ],
                'contents' => [
                    ['role' => 'user', 'parts' => [['text' => $userMessage]]]
                ],
                'generationConfig' => [
                    'temperature' => $temperature,
                    'maxOutputTokens' => $maxTokens,
                ]
            ]
        );

        if ($response->failed()) {
            throw new \Exception('Gemini API error: ' . $response->body());
        }

        $data = $response->json();
        return $data['candidates'][0]['content']['parts'][0]['text'] ?? 'No response from AI.';
    }

    public function cleanTextForChannels(string $text): string
    {
        $text = strip_tags($text);
        $text = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '$1 ($2)', $text);
        $text = str_replace(['**', '##', '###', '####', '```', '`'], '', $text);
        $text = preg_replace('/^\*\s+/m', '- ', $text);
        return trim($text);
    }
}
