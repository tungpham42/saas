<?php

namespace App\Services;

use App\Models\Bot;
use App\Models\SessionStat;
use App\Models\ChatLog;
use App\Mail\ChatTranscriptMail;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class NotificationService
{
    public function processEndedChats(): void
    {
        try {
            $sessions = SessionStat::where('is_emailed', false)
                ->whereHas('bot', function($query) {
                    $query->whereNotNull('email_notify_addresses')
                        ->where('email_notify_addresses', '!=', '');
                })
                ->with(['bot']) // Eager load bot relationship
                ->get();

            if ($sessions->isEmpty()) {
                $this->logInfo('No pending sessions to process');
                return;
            }

            $this->logInfo("Found {$sessions->count()} sessions to process");

            foreach ($sessions as $session) {
                try {
                    DB::beginTransaction();

                    $sent = $this->processSession($session);

                    if ($sent) {
                        DB::commit();
                    } else {
                        DB::rollBack();
                    }
                } catch (\Exception $e) {
                    DB::rollBack();
                    $this->logError('Failed to process session', $session, $e);
                }
            }
        } catch (\Exception $e) {
            $this->logError('Failed to process ended chats', null, $e);
        }
    }

    private function processSession(SessionStat $session): bool
    {
        $bot = $session->bot;
        $timeoutMins = $bot->email_notify_timeout_mins ?? 10;

        // Get last message time
        $lastMsgTime = ChatLog::where('bot_id', $bot->id)
            ->where('session_id', $session->session_id)
            ->max('created_at');

        if (!$lastMsgTime) {
            $this->logInfo("No messages found for session: {$session->session_id}");
            return false;
        }

        $minutesSinceLastMsg = now()->diffInMinutes($lastMsgTime);
        $isTimeout = $minutesSinceLastMsg >= $timeoutMins;

        if (!$isTimeout) {
            $this->logInfo("Session not timed out: {$session->session_id} (Last message: {$minutesSinceLastMsg} mins ago)");
            return false;
        }

        // Send email
        $sent = $this->sendTranscriptEmail($bot, $session);

        if ($sent) {
            $session->update(['is_emailed' => true]);
            $this->logInfo("Email sent successfully for session: {$session->session_id}");
            return true;
        }

        return false;
    }

    private function sendTranscriptEmail(Bot $bot, SessionStat $session): bool
    {
        try {
            $messages = ChatLog::where('bot_id', $bot->id)
                ->where('session_id', $session->session_id)
                ->orderBy('created_at', 'asc')
                ->get();

            if ($messages->isEmpty()) {
                $this->logWarning("No messages found for session: {$session->session_id}");
                return false;
            }

            $emails = array_map('trim', explode(',', $bot->email_notify_addresses));
            $emails = array_filter($emails);

            if (empty($emails)) {
                $this->logWarning("No valid emails for bot: {$bot->id}");
                return false;
            }

            // Queue the email for better performance
            Mail::to($emails)->queue(new ChatTranscriptMail($bot, $session, $messages));

            $this->logInfo("Email queued to: " . implode(', ', $emails), [
                'bot_id' => $bot->id,
                'session_id' => $session->session_id,
                'message_count' => $messages->count()
            ]);

            return true;

        } catch (\Exception $e) {
            $this->logError('Failed to queue email', $session, $e);
            return false;
        }
    }

    private function logInfo(string $message, array $context = []): void
    {
        Log::info("[NotificationService] {$message}", $context);

        if (app()->runningInConsole()) {
            echo "✓ {$message}\n";
        }
    }

    private function logWarning(string $message, array $context = []): void
    {
        Log::warning("[NotificationService] {$message}", $context);

        if (app()->runningInConsole()) {
            echo "⚠ {$message}\n";
        }
    }

    private function logError(string $message, $session = null, \Exception $e = null): void
    {
        $context = ['error' => $e ? $e->getMessage() : 'Unknown error'];

        if ($session) {
            $context['session_id'] = $session->session_id ?? null;
            $context['bot_id'] = $session->bot_id ?? null;
        }

        if ($e) {
            $context['trace'] = $e->getTraceAsString();
        }

        Log::error("[NotificationService] {$message}", $context);

        if (app()->runningInConsole()) {
            echo "✗ {$message}\n";
        }
    }
}
