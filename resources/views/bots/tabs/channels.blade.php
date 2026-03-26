<div x-data="channelsManager()" class="space-y-6">
    <!-- Warm Tip Card -->
    <div class="bg-amber-50 dark:bg-amber-900/20 rounded-2xl p-5 border-l-4 border-amber-400 animate-gentle">
        <div class="flex items-start gap-3">
            <i class="fas fa-lightbulb text-amber-500 text-xl"></i>
            <div>
                <p class="text-amber-800 dark:text-amber-300 font-medium">💝 Friendly Tip</p>
                <p class="text-sm text-amber-600 dark:text-amber-400 mt-1">
                    Copy the Webhook URL from each channel and paste it into the platform's developer console.
                    You can create multiple channels to connect with your customers everywhere!
                </p>
            </div>
        </div>
    </div>

    <!-- Add Channel Form -->
    <div class="card-warm p-6 animate-gentle" style="animation-delay: 0.1s">
        <h3 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle text-amber-500"></i>
            <span>Connect a New Channel 🌐</span>
        </h3>
        <form action="{{ route('bots.channels.store', $bot) }}" method="POST" class="flex flex-col sm:flex-row gap-4">
            @csrf
            <div class="flex-1">
                <select name="channel_type" required class="input-warm w-full">
                    <option value="">✨ Choose a platform</option>
                    <option value="fb">📘 Facebook Messenger</option>
                    <option value="zalo">🔵 Zalo Official Account</option>
                    <option value="zlpn">👤 Zalo Personal</option>
                    <option value="tt">🎵 TikTok Shop</option>
                    <option value="sp">🟠 Shopee Open Platform</option>
                    <option value="wa">🟩 WhatsApp Cloud API</option>
                </select>
            </div>
            <div class="flex-1">
                <input type="text" name="channel_name" placeholder="Give it a cozy name (e.g., Main Fanpage)"
                       class="input-warm w-full">
            </div>
            <div>
                <button type="submit" class="btn-soft px-6 py-3 rounded-xl text-amber-900 font-semibold flex items-center gap-2">
                    <i class="fas fa-heart"></i>
                    <span>Add Channel</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Channels List -->
    <div class="space-y-4">
        @forelse($bot->channels as $channel)
        <div class="card-warm overflow-hidden animate-gentle" x-data="{ showConfig: false }" style="animation-delay: {{ 0.2 + $loop->index * 0.05 }}s">
            <form action="{{ route('bots.channels.update', [$bot, $channel]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="channel_type" value="{{ $channel->channel_type }}">

                <!-- Channel Header -->
                <div class="px-6 py-4 bg-gradient-to-r from-amber-50 to-amber-100 dark:from-gray-800 dark:to-gray-700 border-b border-amber-200 dark:border-gray-600 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3 cursor-pointer" @click="showConfig = !showConfig">
                        <div class="text-3xl">
                            @switch($channel->channel_type)
                                @case('fb') 📘 @break
                                @case('zalo') 🔵 @break
                                @case('zlpn') 👤 @break
                                @case('tt') 🎵 @break
                                @case('sp') 🟠 @break
                                @case('wa') 🟩 @break
                                @default 💬
                            @endswitch
                        </div>
                        <div>
                            <h4 class="font-bold text-amber-800 dark:text-amber-200">{{ $channel->channel_name }}</h4>
                            <p class="text-xs text-amber-500 dark:text-amber-400">ID: {{ $channel->id }} • {{ ucfirst($channel->channel_type) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $channel->is_active ? 'checked' : '' }}
                                   class="w-4 h-4 text-amber-500 rounded focus:ring-amber-500">
                            <span class="text-sm text-amber-600 dark:text-amber-400">
                                <i class="fas {{ $channel->is_active ? 'fa-heart text-red-500' : 'fa-heart-broken' }}"></i>
                                {{ $channel->is_active ? 'Active' : 'Inactive' }}
                            </span>
                        </label>
                        <button type="submit" class="text-amber-600 hover:text-amber-700 text-sm font-medium">
                            <i class="fas fa-save"></i> Save
                        </button>
                        <button type="button" @click="showConfig = !showConfig" class="text-amber-500 hover:text-amber-600">
                            <i class="fas" :class="showConfig ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                </div>

                <!-- Channel Configuration (Collapsible) -->
                <div x-show="showConfig" x-collapse class="p-6 border-t border-amber-100 dark:border-gray-700 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-tag mr-2 text-amber-500"></i>Channel Name
                        </label>
                        <input type="text" name="channel_name" value="{{ $channel->channel_name }}"
                               class="input-warm w-full max-w-md">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-link mr-2 text-amber-500"></i>Webhook URL
                        </label>
                        <div class="flex gap-2">
                            <input type="text" id="webhook-{{ $channel->id }}" readonly
                                   value="{{ $channel->getWebhookUrl() }}"
                                   class="flex-1 px-4 py-2 bg-amber-50 dark:bg-gray-700 border border-amber-200 dark:border-gray-600 rounded-xl font-mono text-sm text-amber-700 dark:text-amber-300">
                            <button type="button" onclick="copyWebhook('webhook-{{ $channel->id }}', this)"
                                    class="px-4 py-2 bg-amber-100 dark:bg-gray-600 rounded-xl hover:bg-amber-200 dark:hover:bg-gray-500 transition text-amber-600">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    @switch($channel->channel_type)
                        @case('fb')
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-shield-alt mr-2 text-amber-500"></i>Verify Token
                                </label>
                                <input type="text" name="fb_verify_token" value="{{ $channel->config['fb_verify_token'] ?? '' }}"
                                       class="input-warm w-full max-w-md">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-key mr-2 text-amber-500"></i>Page Access Token
                                </label>
                                <textarea name="fb_page_token" rows="3"
                                          class="input-warm w-full max-w-md font-mono text-sm">{{ $channel->config['fb_page_token'] ?? '' }}</textarea>
                                <p class="text-xs text-amber-500 mt-1">Find this in your Facebook Developer Dashboard</p>
                            </div>
                            @break

                        @case('zalo')
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-key mr-2 text-amber-500"></i>OA Access Token
                                </label>
                                <textarea name="zalo_access_token" rows="3"
                                          class="input-warm w-full max-w-md font-mono text-sm">{{ $channel->config['zalo_access_token'] ?? '' }}</textarea>
                                <p class="text-xs text-amber-500 mt-1">Get from Zalo Developer Console</p>
                            </div>
                            @break

                        @case('wa')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                        <i class="fas fa-shield-alt mr-2 text-amber-500"></i>Verify Token
                                    </label>
                                    <input type="text" name="whatsapp_verify_token" value="{{ $channel->config['whatsapp_verify_token'] ?? '' }}"
                                           class="input-warm w-full">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                        <i class="fas fa-phone mr-2 text-amber-500"></i>Phone Number ID
                                    </label>
                                    <input type="text" name="whatsapp_phone_number_id" value="{{ $channel->config['whatsapp_phone_number_id'] ?? '' }}"
                                           class="input-warm w-full">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-key mr-2 text-amber-500"></i>System User Token
                                </label>
                                <textarea name="whatsapp_token" rows="2"
                                          class="input-warm w-full max-w-md font-mono text-sm">{{ $channel->config['whatsapp_token'] ?? '' }}</textarea>
                                <p class="text-xs text-amber-500 mt-1">From Meta Business Suite</p>
                            </div>
                            @break

                        @case('sp')
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-store mr-2 text-amber-500"></i>Shop ID
                                </label>
                                <input type="text" name="shopee_shop_id" value="{{ $channel->config['shopee_shop_id'] ?? '' }}"
                                       class="input-warm w-full max-w-md">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-key mr-2 text-amber-500"></i>Access Token
                                </label>
                                <textarea name="shopee_access_token" rows="2"
                                          class="input-warm w-full max-w-md font-mono text-sm">{{ $channel->config['shopee_access_token'] ?? '' }}</textarea>
                            </div>
                            @break

                        @case('tt')
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-key mr-2 text-amber-500"></i>Access Token
                                </label>
                                <textarea name="tiktok_access_token" rows="3"
                                          class="input-warm w-full max-w-md font-mono text-sm">{{ $channel->config['tiktok_access_token'] ?? '' }}</textarea>
                            </div>
                            @break

                        @case('zlpn')
                            <div>
                                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                                    <i class="fas fa-key mr-2 text-amber-500"></i>API Key / Token
                                </label>
                                <textarea name="zalo_personal_token" rows="3"
                                          class="input-warm w-full max-w-md font-mono text-sm">{{ $channel->config['zalo_personal_token'] ?? '' }}</textarea>
                                <p class="text-xs text-amber-500 mt-1">Format: API_URL|TOKEN or just TOKEN</p>
                            </div>
                            @break
                    @endswitch
                </div>
            </form>

            <!-- Delete Button -->
            <div class="px-6 py-3 bg-amber-50/50 dark:bg-gray-800/50 border-t border-amber-100 dark:border-gray-700 flex justify-end">
                <form action="{{ route('bots.channels.destroy', [$bot, $channel]) }}" method="POST" onsubmit="return confirmDeleteChannel(this)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-500 hover:text-red-600 text-sm font-medium flex items-center gap-1 transition">
                        <i class="fas fa-trash-alt"></i>
                        <span>Remove Channel</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="card-warm p-12 text-center animate-gentle">
            <div class="text-6xl mb-4">📱</div>
            <h3 class="text-xl font-bold text-amber-800 dark:text-amber-200 mb-2">No Channels Connected Yet</h3>
            <p class="text-amber-600 dark:text-amber-400 mb-6">Connect your bot to social platforms to reach more customers and spread kindness! 💝</p>
            <button onclick="document.querySelector('select[name=\"channel_type\"]').focus()"
                    class="btn-soft inline-flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>Add Your First Channel</span>
            </button>
        </div>
        @endforelse
    </div>
</div>

@push('scripts')
<script>
function channelsManager() {
    return {
        showConfig: false
    }
}

function copyWebhook(elementId, button) {
    const input = document.getElementById(elementId);
    input.select();
    input.setSelectionRange(0, 99999);
    document.execCommand('copy');
    const originalHtml = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check text-green-500"></i>';
    setTimeout(() => {
        button.innerHTML = originalHtml;
    }, 2000);

    Swal.fire({
        icon: 'success',
        title: 'Copied! 📋',
        text: 'Webhook URL copied to clipboard',
        toast: true,
        timer: 2000,
        showConfirmButton: false,
        position: 'top-end'
    });
}

function confirmDeleteChannel(form) {
    Swal.fire({
        title: 'Remove Channel? 💔',
        text: 'This channel will be disconnected. You can always add it back later.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, remove it',
        cancelButtonText: 'Keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endpush
