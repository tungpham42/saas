@extends('layouts.app')

@section('title', __('Community Members - SaaS AI Chatbot'))

@section('content')
<div class="space-y-8">
    <div class="flex flex-wrap justify-between items-center gap-4 animate-gentle">
        <div>
            <h1 class="text-3xl font-bold text-amber-800 dark:text-amber-200">{{ __('Community Members 🌟') }}</h1>
            <p class="text-amber-600 dark:text-amber-400 mt-1">{{ __('Meet the wonderful people using our platform') }}</p>
        </div>
        <a href="{{ route('users.create') }}" class="btn-soft inline-flex items-center gap-2">
            <i class="fas fa-user-plus"></i>
            <span>{{ __('Welcome New Member') }}</span>
        </a>
    </div>

    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.1s">
        <div class="overflow-x-auto">
            <table class="table-warm">
                <thead>
                    <tr>
                        <th>{{ __('Member') }}</th>
                        <th>{{ __('Email') }}</th>
                        <th>{{ __('Bots') }}</th>
                        <th>{{ __('Plan') }}</th>
                        <th>{{ __('Status') }}</th>
                        <th>{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr class="hover:bg-amber-50 dark:hover:bg-gray-800 transition-colors">
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="gradient-warm rounded-full w-10 h-10 flex items-center justify-center">
                                    <span class="text-amber-900 font-bold">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                                </div>
                                <span class="font-medium text-amber-800 dark:text-amber-200">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="text-amber-700 dark:text-amber-300">{{ $user->email }}</td>
                        <td>
                            <span class="inline-flex items-center gap-1 px-3 py-1 bg-amber-50 dark:bg-gray-700 rounded-full text-sm">
                                <i class="fas fa-robot text-amber-500"></i>
                                {{ $user->bots_count }} / {{ $user->bot_limit }}
                            </span>
                        </td>
                        <td>
                            <div class="text-sm">
                                <span class="font-medium text-amber-800 dark:text-amber-200">${{ max(0, $user->bot_limit - 1) * 2 }}/mo</span>
                                <div class="text-xs text-amber-500">{{ __('Base + ') }}{{ max(0, $user->bot_limit - 1) }} bots</div>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge {{ $user->paypal_sub_status === 'Active' ? 'status-active' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                <i class="fas {{ $user->paypal_sub_status === 'Active' ? 'fa-heart' : 'fa-clock' }}"></i>
                                {{ $user->paypal_sub_status }}
                            </span>
                        </td>
                        <td>
                            <div class="flex gap-2">
                                <a href="{{ route('users.edit', $user) }}" class="text-amber-600 hover:text-amber-700 transition" title="{{ __('Edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline" onsubmit="return confirmDeleteUser(this)">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-600 transition" title="{{ __('Remove') }}">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-12 text-center">
                            <div class="text-6xl mb-4">🌟</div>
                            <p class="text-amber-600 dark:text-amber-400">{{ __('No members yet') }}</p>
                            <p class="text-sm text-amber-500 mt-1">{{ __('Invite someone to join the community') }}</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="px-6 py-4 border-t border-amber-100 dark:border-gray-700">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function confirmDeleteUser(form) {
    Swal.fire({
        title: '{{ __("Remove member? 💔") }}',
        text: '{{ __("This will delete all their bots and memories. This cannot be undone.") }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __("Yes, remove") }}',
        cancelButtonText: '{{ __("Cancel") }}'
    }).then((result) => {
        if (result.isConfirmed) form.submit();
    });
    return false;
}
</script>
@endpush
@endsection
