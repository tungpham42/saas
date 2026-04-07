@extends('layouts.app')

@section('title', __('Lead Details') . ' - ' . $lead->customer_name)

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <div class="mb-6">
        <a href="{{ route('bots.leads', $bot) }}" class="inline-flex items-center text-gray-600 hover:text-gray-900">
            <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            {{ __('Back to Leads') }}
        </a>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Lead Information') }}</h2>
        </div>
        <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('Name') }}</label>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $lead->customer_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('Phone') }}</label>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $lead->customer_phone }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('Session ID') }}</label>
                <code class="mt-1 block text-sm font-mono bg-gray-100 p-2 rounded">{{ $lead->session_id }}</code>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('Created At') }}</label>
                <p class="mt-1 text-gray-900">{{ $lead->created_at->format('F d, Y H:i:s') }}</p>
                <p class="text-sm text-gray-500">{{ $lead->created_at->diffForHumans() }}</p>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50 flex justify-between items-center">
            <h2 class="text-lg font-medium text-gray-900">{{ __('Chat History') }}</h2>
            <a href="{{ route('bots.history', $bot) }}?session_id={{ urlencode($lead->session_id) }}"
               class="text-blue-600 hover:text-blue-800 text-sm">
                {{ __('View Full Conversation →') }}
            </a>
        </div>
        <div class="p-6 space-y-4 max-h-96 overflow-y-auto">
            @php
                $messages = $bot->chatLogs()
                    ->where('session_id', $lead->session_id)
                    ->orderBy('created_at', 'asc')
                    ->limit(20)
                    ->get();
            @endphp

            @forelse($messages as $msg)
                <div class="flex {{ $msg->role === 'user' ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-[70%] {{ $msg->role === 'user' ? 'bg-blue-500 text-white' : ($msg->role === 'admin' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800') }} rounded-lg p-3">
                        <div class="text-xs opacity-75 mb-1">
                            {{ ucfirst($msg->role) }} • {{ $msg->created_at->format('H:i:s') }}
                        </div>
                        <div class="text-sm whitespace-pre-wrap">{{ $msg->content }}</div>
                    </div>
                </div>
            @empty
                <div class="text-center text-gray-500 py-8">
                    {{ __('No messages found for this lead') }}
                </div>
            @endforelse
        </div>
    </div>

    <div class="flex justify-end gap-3">
        <a href="{{ route('bots.history', $bot) }}?session_id={{ urlencode($lead->session_id) }}"
           class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
            {{ __('View Full Conversation') }}
        </a>
        <a href="{{ route('bots.leads.export', $bot) }}?session_id={{ urlencode($lead->session_id) }}"
           class="px-4 py-2 bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200">
            {{ __('Export This Lead') }}
        </a>
    </div>
</div>
@endsection
