<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\SessionStat;
use App\Models\ChatLog;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    public function processEndedChats(): void
    {
        $sessions = SessionStat::where('is_emailed', false)
            ->whereHas('bot', function($query) {
                $query->whereNotNull('email_notify_addresses')
                    ->where('email_notify_addresses', '!=', '');
            })
            ->get();

        foreach ($sessions as $session) {
            $bot = $session->bot;
            $timeoutMins = $bot->email_notify_timeout_mins ?? 10;

            $lastMsgTime = ChatLog::where('bot_id', $bot->id)
                ->where('session_id', $session->session_id)
                ->max('created_at');

            if (!$lastMsgTime) continue;

            $isTimeout = now()->diffInMinutes($lastMsgTime) >= $timeoutMins;

            if ($isTimeout) {
                $this->sendTranscriptEmail($bot, $session);
                $session->update(['is_emailed' => true]);
            }
        }
    }

    private function sendTranscriptEmail(Bot $bot, SessionStat $session): void
    {
        $messages = ChatLog::where('bot_id', $bot->id)
            ->where('session_id', $session->session_id)
            ->orderBy('created_at', 'asc')
            ->get();

        if ($messages->isEmpty()) return;

        $emails = array_map('trim', explode(',', $bot->email_notify_addresses));
        $subject = "[SaaS AI Chatbot] Chat Transcript: {$session->session_id} - {$bot->name}";

        $html = view('emails.chat-transcript', [
            'bot' => $bot,
            'session' => $session,
            'messages' => $messages
        ])->render();

        Mail::html($html, function($mail) use ($emails, $subject) {
            $mail->to($emails)->subject($subject);
        });
    }
}
