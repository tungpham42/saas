@extends('layouts.app')

@section('title', 'Chat History - ' . $bot->name)

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-4 animate-gentle">
        <a href="{{ route('bots.show', $bot) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-amber-800 dark:text-amber-200">Chat History 📜</h1>
            <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">Browse past conversations with {{ $bot->name }}</p>
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
