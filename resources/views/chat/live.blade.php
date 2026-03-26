@extends('layouts.app')

@section('title', 'Live Chat - ' . $bot->name)

@section('content')
<div class="space-y-4">
    <div class="flex items-center gap-4 animate-gentle">
        <a href="{{ route('bots.show', $bot) }}" class="text-amber-600 hover:text-amber-700 dark:text-amber-400 transition">
            <i class="fas fa-arrow-left text-xl"></i>
        </a>
        <div>
            <h1 class="text-2xl font-bold text-amber-800 dark:text-amber-200">Live Chat 💬</h1>
            <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">Real-time conversations with {{ $bot->name }}</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse-soft"></div>
            <span class="text-sm text-green-600 dark:text-green-400 font-medium">Live & Connected</span>
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
        'isLive' => true
    ])
</div>
@endsection
