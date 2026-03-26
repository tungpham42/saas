@extends('layouts.app')

@section('title', 'My Bots - SaaS AI Chatbot')

@section('content')
<div class="space-y-8">
    <!-- Header Section -->
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-up">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">My Chatbots</h1>
            <p class="text-gray-600 dark:text-gray-400 mt-1">Manage and monitor your AI assistants</p>
        </div>

        @if($canCreate)
        <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl p-1">
            <form action="{{ route('bots.store') }}" method="POST" class="flex gap-3">
                @csrf
                <input type="text" name="name" placeholder="Client Name / Website..."
                       class="px-4 py-3 border-0 rounded-xl bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-400 focus:ring-2 focus:ring-blue-500 transition-all w-64"
                       required>
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Create Bot</span>
                </button>
            </form>
        </div>
        @else
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-xl p-4 border border-amber-200 dark:border-amber-800">
            <div class="flex items-center gap-3">
                <i class="fas fa-exclamation-triangle text-amber-600"></i>
                <p class="text-amber-700 dark:text-amber-400 text-sm">You have reached your bot limit. <a href="#" class="font-semibold underline">Upgrade your plan</a></p>
            </div>
        </div>
        @endif
    </div>

    <!-- Quota Card -->
    @if(!auth()->user()->isAdmin())
    <div class="gradient-primary rounded-2xl p-6 text-white shadow-lg animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="flex justify-between items-center">
            <div>
                <p class="text-white/80 text-sm">Bot Usage Quota</p>
                <p class="text-3xl font-bold">{{ auth()->user()->bots()->count() }} / {{ auth()->user()->bot_limit }}</p>
            </div>
            <div class="bg-white/20 rounded-full w-16 h-16 flex items-center justify-center">
                <i class="fas fa-chart-pie text-2xl"></i>
            </div>
        </div>
        <div class="w-full bg-white/20 rounded-full h-2 mt-4">
            <div class="bg-white rounded-full h-2" style="width: {{ (auth()->user()->bots()->count() / max(1, auth()->user()->bot_limit)) * 100 }}%"></div>
        </div>
        <p class="text-white/70 text-sm mt-2">{{ $remainingSlots }} slot{{ $remainingSlots != 1 ? 's' : '' }} remaining</p>
    </div>
    @endif

    <!-- Bots Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($bots as $bot)
        <!-- Bot Card - Update the card styling -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg hover:shadow-xl transition-all card-hover animate-fade-in-up overflow-hidden">
            <div class="gradient-primary p-4">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-xl p-2">
                            <i class="fas fa-robot text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white font-bold text-lg">{{ $bot->name }}</h3>
                            <p class="text-white/70 text-xs">Created {{ $bot->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <span class="bg-black/20 text-white text-xs px-2 py-1 rounded-full">User #{{ $bot->user_id }}</span>
                    @endif
                </div>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">API Key</label>
                    <div class="mt-1 flex items-center gap-2">
                        <code class="flex-1 text-xs font-mono bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300">{{ substr($bot->api_key, 0, 20) }}...</code>
                        <button onclick="copyToClipboard('{{ $bot->api_key }}', this)"
                                class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-600 dark:text-gray-400">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div>
                    <label class="text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Embed Code</label>
                    <div class="mt-1 flex items-center gap-2">
                        <input type="text" id="embed-{{ $bot->id }}"
                            value='<script src="{{ route('embed.js', ['api_key' => $bot->api_key]) }}" defer></script>'
                            class="flex-1 text-xs font-mono bg-gray-100 dark:bg-gray-700 px-3 py-2 rounded-lg text-gray-700 dark:text-gray-300"
                            readonly>
                        <button onclick="copyToClipboardInput('embed-{{ $bot->id }}', this)"
                                class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition text-gray-600 dark:text-gray-400">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="flex gap-2 pt-2">
                    <a href="{{ route('bots.show', $bot) }}"
                    class="flex-1 btn-primary text-center py-2 rounded-xl text-white font-medium flex items-center justify-center gap-2">
                        <i class="fas fa-cog"></i>
                        <span>Manage</span>
                    </a>
                    <form action="{{ route('bots.destroy', $bot) }}" method="POST" class="flex-1" onsubmit="return confirmDelete(this)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-500 hover:bg-red-600 text-white py-2 rounded-xl font-medium flex items-center justify-center gap-2 transition">
                            <i class="fas fa-trash-alt"></i>
                            <span>Delete</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center shadow-lg">
                <div class="gradient-primary rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-robot text-white text-3xl"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No bots yet</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Create your first AI chatbot to get started</p>
                @if($canCreate)
                <button onclick="document.querySelector('form input[name=\'name\']').focus()"
                        class="btn-primary px-6 py-3 rounded-xl text-white font-semibold inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Create Your First Bot</span>
                </button>
                @endif
            </div>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text, button) {
    navigator.clipboard.writeText(text);
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-green-500"></i>';
    setTimeout(() => {
        button.innerHTML = originalHtml;
    }, 2000);
}

function copyToClipboardInput(elementId, button) {
    const input = document.getElementById(elementId);
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-green-500"></i>';
    setTimeout(() => {
        button.innerHTML = originalHtml;
    }, 2000);
}

function confirmDelete(form) {
    Swal.fire({
        title: 'Delete Bot?',
        text: 'This action cannot be undone. All chat history and data will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endpush
@endsection
