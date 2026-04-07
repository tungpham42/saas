@php
    $isActive = $isActive ?? false;
    $lastTime = \Carbon\Carbon::parse($session->last_time);
@endphp

<a href="#"
   data-session-id="{{ $session->session_id }}"
   @click.prevent="selectSession('{{ $session->session_id }}')"
   class="session-item block p-3 rounded-xl transition-all {{ $isActive ? 'gradient-warm text-amber-900 shadow-md' : 'hover:bg-amber-50 dark:hover:bg-gray-800' }}">
    <div class="flex items-start justify-between">
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2">
                <span class="text-lg">{{ $sessionInfo['icon'] ?? '💬' }}</span>
                <span class="font-mono text-xs truncate {{ $isActive ? 'text-amber-900' : 'text-amber-600 dark:text-amber-400' }}">
                    {{ substr($session->session_id, 0, 20) }}...
                </span>
            </div>
            @if(isset($sessionInfo['channel_name']))
            <p class="text-xs {{ $isActive ? 'text-amber-800/70' : 'text-amber-400' }} mt-1">
                <i class="fas fa-link mr-1"></i>{{ $sessionInfo['channel_name'] }}
            </p>
            @endif
            <div class="flex items-center gap-3 mt-2 text-xs {{ $isActive ? 'text-amber-800/70' : 'text-amber-400' }}">
                <span>
                    <i class="far fa-clock mr-1"></i>{{ $lastTime->format('M d, H:i') }}
                </span>
                <span>
                    <i class="fas fa-comment mr-1"></i>{{ $session->msgs }} {{ __('msgs') }}
                </span>
            </div>
        </div>
        @if(isset($isLive) && $isLive && !($sessionInfo['has_recent_admin'] ?? false))
            <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse-soft"></div>
        @endif
    </div>
</a>
