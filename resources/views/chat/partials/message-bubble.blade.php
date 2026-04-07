@php
    $isUser = $message->role === 'user'; // The website visitor
    $isAdmin = $message->role === 'admin'; // You (Dashboard User)
    $isBot = $message->role === 'bot';

    // Admin (You) should be on the right, Customers on the left
    $align = $isAdmin ? 'justify-end' : 'justify-start';

    $bgClass = $isAdmin ? 'gradient-warm text-amber-900' :
               ($isUser ? 'bg-white dark:bg-gray-700 border border-amber-200 dark:border-gray-600 text-amber-800 dark:text-amber-200' :
               'bg-green-100 dark:bg-green-900/30 text-green-800 dark:text-green-300 border border-green-200 dark:border-green-800');

    $roleLabel = $isAdmin ? __('You') : ($isUser ? __('Customer') : __('🤖 AI Friend'));
    $roleIcon = $isAdmin ? 'fa-user-tie' : ($isUser ? 'fa-user' : 'fa-robot');
    $time = \Carbon\Carbon::parse($message->created_at)->format('H:i');
    $date = \Carbon\Carbon::parse($message->created_at)->format('M d, Y');
    $isFirstInGroup = $loop->first ?? false;

    // Convert raw URLs into hyperlink icons
    $formattedContent = nl2br(e($message->content));
    $formattedContent = preg_replace('/(https?:\/\/[^\s]+)/', '<a href="$1" target="_blank" class="text-blue-500 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300 mx-1" title="' . __('Open Link') . '"><i class="fas fa-link"></i></a>', $formattedContent);
@endphp

<div class="flex {{ $align }} animate-gentle group">
    <div class="max-w-[75%] {{ $bgClass }} rounded-2xl shadow-sm overflow-hidden">
        <div class="px-4 py-2 {{ $isAdmin ? 'bg-amber-100/50 dark:bg-amber-900/30' : ($isUser ? 'bg-amber-50 dark:bg-gray-800' : 'bg-green-100/50 dark:bg-green-900/20') }}">
            <div class="flex items-center gap-2 text-xs {{ $isAdmin ? 'text-amber-700' : ($isUser ? 'text-amber-500 dark:text-amber-400' : 'text-green-700 dark:text-green-400') }}">
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
                {!! $formattedContent !!}
            </div>
        </div>
        <div class="px-4 py-1 text-right opacity-0 group-hover:opacity-100 transition">
            <button onclick="copyMessage('{{ addslashes($message->content) }}')"
                    class="text-xs {{ $isAdmin ? 'text-amber-600' : ($isUser ? 'text-amber-400' : 'text-green-600') }} hover:text-amber-600 transition">
                <i class="fas fa-copy"></i> {{ __('Copy') }}
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
        title: '{{ __('Copied! 📋') }}',
        text: '{{ __('Message copied to clipboard') }}',
        toast: true,
        timer: 2000,
        showConfirmButton: false,
        position: 'top-end'
    });
}
</script>
@endpush
