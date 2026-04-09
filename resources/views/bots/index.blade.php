@extends('layouts.app')

@section('title', __('My Bots') . ' - SaaS AI Chatbot')

@section('content')
<div class="space-y-8">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-gentle">
        <div>
            <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __('My Chatbots 🤖') }}</h1>
            <p class="text-amber-600 dark:text-amber-400 mt-1">{{ __('Your amazing AI assistants, ready to help') }}</p>
        </div>

        @if($canCreate)
        <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-2">
            <form action="{{ route('bots.store') }}" method="POST" class="flex gap-3">
                @csrf
                <input type="text" name="name" placeholder="{{ __('Give your bot a name...') }}"
                       class="input-warm w-64"
                       required>
                <button type="submit" class="btn-soft inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>{{ __('Create Bot') }}</span>
                </button>
            </form>
        </div>
        @else
        <div class="bg-orange-50 dark:bg-orange-900/20 rounded-2xl p-4 border border-orange-200 dark:border-orange-800">
            <div class="flex items-center gap-3">
                <i class="fas fa-coffee text-orange-500"></i>
                <p class="text-orange-700 dark:text-orange-400 text-sm">{{ __('You\'ve reached your bot limit.') }} <a href="#" class="font-semibold underline">{{ __('Upgrade your plan') }}</a>{{ __(' to create more') }}</p>
            </div>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
        @forelse($bots as $bot)
        <div class="card-warm overflow-hidden animate-gentle group">
            <div class="gradient-warm p-5">
                <div class="flex justify-between items-start">
                    <div class="flex items-center gap-3">
                        <div class="bg-white/20 rounded-xl p-2 group-hover:scale-110 transition-transform">
                            <i class="fas fa-robot text-amber-900 text-xl"></i>
                        </div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-amber-900 font-bold text-lg">{{ $bot->name }}</h3>
                            @if($bot->is_active)
                                <span class="bg-green-100 text-green-700 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">{{ __('Active') }}</span>
                            @else
                                <span class="bg-gray-200 text-gray-600 text-[10px] px-2 py-0.5 rounded-full font-bold uppercase tracking-wider">{{ __('Inactive') }}</span>
                            @endif
                            <p class="text-amber-800/70 text-xs">{{ __('Born ') }} {{ $bot->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @if(auth()->user()->isAdmin())
                    <span class="bg-black/20 text-amber-900 text-xs px-2 py-1 rounded-full">{{ __('User #') }}{{ $bot->user_id }}</span>
                    @endif
                </div>
            </div>

            <div class="p-5 space-y-4">
                <div>
                    <label class="text-xs font-semibold text-amber-500 uppercase tracking-wider">{{ __('API Key') }}</label>
                    <div class="mt-1 flex items-center gap-2">
                        <code class="flex-1 text-xs font-mono bg-amber-50 dark:bg-gray-800 px-3 py-2 rounded-xl text-amber-700 dark:text-amber-300">{{ substr($bot->api_key, 0, 20) }}...</code>
                        <button onclick="copyToClipboard('{{ $bot->api_key }}', this)"
                                class="p-2 bg-amber-50 dark:bg-gray-800 rounded-xl hover:bg-amber-100 dark:hover:bg-gray-700 transition text-amber-600" title="{{ __('Copy API Key') }}">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>

                <div class="flex flex-col gap-2 pt-2">
                    <a href="{{ route('bots.show', $bot) }}"
                    class="w-full btn-soft text-center py-2 rounded-xl inline-flex items-center justify-center gap-2">
                        <i class="fas fa-heart"></i>
                        <span>{{ __('Care for Bot') }}</span>
                    </a>
                    <div class="flex gap-2">
                        <form action="{{ route('bots.toggle-status', $bot) }}" method="POST" class="flex-1">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="w-full {{ $bot->is_active ? 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200' : 'bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 hover:bg-green-200' }} py-2 rounded-xl font-medium transition inline-flex items-center justify-center gap-2" title="{{ $bot->is_active ? __('Deactivate') : __('Activate') }}">
                                <i class="fas fa-power-off"></i>
                                <span>{{ $bot->is_active ? __('Deactivate') : __('Activate') }}</span>
                            </button>
                        </form>
                        <form action="{{ route('bots.destroy', $bot) }}" method="POST" class="flex-1" onsubmit="return confirmDelete(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="w-full bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 py-2 rounded-xl font-medium hover:bg-red-200 dark:hover:bg-red-900/50 transition inline-flex items-center justify-center gap-2">
                                <i class="fas fa-trash-alt"></i>
                                <span>{{ __('Say Goodbye') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="card-warm p-12 text-center">
                <div class="gradient-warm rounded-full w-24 h-24 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-robot text-amber-900 text-4xl"></i>
                </div>
                <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">{{ __('No bots yet') }}</h3>
                <p class="text-amber-600 dark:text-amber-400 mb-6">{{ __('Create your first AI chatbot to get started on your journey') }}</p>
                @if($canCreate)
                <button onclick="document.querySelector('form input[name=\'name\']').focus()"
                        class="btn-soft inline-flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>{{ __('Create Your First Bot') }}</span>
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

function confirmDelete(form) {
    Swal.fire({
        title: '{{ __('Say goodbye? 💔') }}',
        text: '{{ __('This action cannot be undone. All chat history and memories will be lost.') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __('Yes, delete it') }}',
        cancelButtonText: '{{ __('Cancel') }}'
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
