@php
    $isUser = $message->role === 'user';
    $isAdmin = $message->role === 'admin';
    $isBot = $message->role === 'bot';

    $align = $isUser ? 'justify-end' : 'justify-start';
    $bgClass = $isUser ? 'gradient-warm text-amber-900' :
               ($isAdmin ? 'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800' :
               'bg-white dark:bg-gray-700 border border-amber-200 dark:border-gray-600 text-amber-800 dark:text-amber-200');
    $roleLabel = $isUser ? 'You' : ($isAdmin ? '✨ Helper' : '🤖 AI Friend');
    $roleIcon = $isUser ? 'fa-user' : ($isAdmin ? 'fa-user-tie' : 'fa-robot');
    $time = \Carbon\Carbon::parse($message->created_at)->format('H:i');
    $date = \Carbon\Carbon::parse($message->created_at)->format('M d, Y');
    $isFirstInGroup = $loop->first ?? false;
@endphp

<div class="flex {{ $align }} animate-gentle group">
    <div class="max-w-[75%] {{ $bgClass }} rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 py-2 {{ $isUser ? 'bg-amber-100/50 dark:bg-amber-900/30' : ($isAdmin ? 'bg-green-100/50 dark:bg-green-900/20' : 'bg-amber-50 dark:bg-gray-800') }}">
            <div class="flex items-center gap-2 text-xs {{ $isUser ? 'text-amber-700' : ($isAdmin ? 'text-green-700 dark:text-green-400' : 'text-amber-500 dark:text-amber-400') }}">
                <i class="fas {{ $roleIcon }}"></i>
                <span class="font-medium">{{ $roleLabel }}</span>
                <span>•</span>
                <span><i class="far fa-clock"></i> {{ $time }}</span>
                @if($isFirstInGroup)
                    <span>•</span>
                    <span><i class="far fa-calendar-alt"></i> {{ $date }}</span>
                @endif
            </div>
        </div>
        <div class="px-4 py-3">
            <div class="text-sm whitespace-pre-wrap break-words leading-relaxed">
                {!! nl2br(e($message->content)) !!}
            </div>
        </div>
        <div class="px-4 py-1 text-right opacity-0 group-hover:opacity-100 transition">
            <button onclick="copyMessage('{{ addslashes($message->content) }}')"
                    class="text-xs {{ $isUser ? 'text-amber-600' : ($isAdmin ? 'text-green-600' : 'text-amber-400') }} hover:text-amber-600 transition">
                <i class="fas fa-copy"></i> Copy
            </button>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyMessage(text) {
    navigator.clipboard.writeText(text);
    Swal.fire({
        icon: 'success',
        title: 'Copied! 📋',
        text: 'Message copied to clipboard',
        toast: true,
        timer: 2000,
        showConfirmButton: false,
        position: 'top-end'
    });
}
</script>
@endpush
