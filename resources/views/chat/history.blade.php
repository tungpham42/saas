@extends('layouts.app')

@section('title', 'Chat History - ' . $bot->name)

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-4 animate-fade-in-up">
        <a href="{{ route('bots.show', $bot) }}" class="text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Chat History</h1>
            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Browse past conversations from {{ $bot->name }}</p>
        </div>
    </div>

    @include('chat.partials.chat-layout', [
        'bot' => $bot,
        'sessions' => $sessions,
        'messages' => $messages,
        'selectedSession' => $selectedSession,
        'datePreset' => $datePreset,
        'filterDate' => $filterDate,
        'channels' => $channels,
        'isLive' => false
    ])
</div>
@endsection
