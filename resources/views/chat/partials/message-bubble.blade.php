@php
    $isUser = $message->role === 'user';
    $isAdmin = $message->role === 'admin';
    $isBot = $message->role === 'bot';

    $align = $isUser ? 'justify-end' : 'justify-start';
    $bgClass = $isUser ? 'bg-gradient-to-r from-blue-500 to-blue-600 text-white' :
               ($isAdmin ? 'bg-gradient-to-r from-green-500 to-green-600 text-white' : 'bg-white dark:bg-gray-700 border border-gray-200 dark:border-gray-600');
    $roleLabel = $isUser ? 'You' : ($isAdmin ? 'Admin' : 'AI Assistant');
    $roleIcon = $isUser ? 'fa-user' : ($isAdmin ? 'fa-user-tie' : 'fa-robot');
    $time = \Carbon\Carbon::parse($message->created_at)->format('H:i');
    $date = \Carbon\Carbon::parse($message->created_at)->format('M d, Y');
    $isFirstInGroup = $loop->first ?? false;
@endphp

<div class="flex {{ $align }} animate-fade-in-up group">
    <div class="max-w-[75%] {{ $bgClass }} rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 py-2 {{ $isUser ? 'bg-blue-600/20' : ($isAdmin ? 'bg-green-600/20' : 'bg-gray-100 dark:bg-gray-600/30') }}">
            <div class="flex items-center gap-2 text-xs {{ $isUser ? 'text-blue-100' : ($isAdmin ? 'text-green-100' : 'text-gray-500 dark:text-gray-400') }}">
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
                    class="text-xs {{ $isUser ? 'text-blue-200' : ($isAdmin ? 'text-green-200' : 'text-gray-400') }} hover:text-white transition">
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
        title: 'Copied!',
        text: 'Message copied to clipboard',
        toast: true,
        timer: 2000,
        showConfirmButton: false,
        position: 'top-end'
    });
}
</script>
@endpush

<style>
@keyframes fade-in-up {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in-up {
    animation: fade-in-up 0.3s ease-out;
}
</style>
