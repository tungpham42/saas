<div x-data="channelsManager()" class="space-y-6">
    <!-- Tip Card -->
    <div class="bg-gradient-to-r from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-2xl p-5 border border-blue-200 dark:border-blue-800 animate-fade-in-up">
        <div class="flex items-start gap-3">
            <i class="fas fa-lightbulb text-blue-500 text-xl"></i>
            <div>
                <p class="text-blue-800 dark:text-blue-300 font-medium">Quick Tip</p>
                <p class="text-sm text-blue-700 dark:text-blue-400 mt-1">
                    Copy the Webhook URL provided in each channel card and paste it into the respective platform's developer console.
                    You can create multiple channels of the same type.
                </p>
            </div>
        </div>
    </div>

    <!-- Add Channel Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6 animate-fade-in-up" style="animation-delay: 0.1s">
        <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle text-green-500"></i>
            <span>Add New Channel</span>
        </h3>
        <form action="{{ route('bots.channels.store', $bot) }}" method="POST" class="flex flex-col sm:flex-row gap-4">
            @csrf
            <div class="flex-1">
                <select name="channel_type" required class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500">
                    <option value="">-- Select Channel --</option>
                    <option value="fb">📘 Facebook Messenger</option>
                    <option value="zalo">🔵 Zalo Official Account</option>
                    <option value="zlpn">👤 Zalo Personal</option>
                    <option value="tt">🎵 TikTok Shop</option>
                    <option value="sp">🟠 Shopee Open Platform</option>
                    <option value="wa">🟩 WhatsApp Cloud API</option>
                </select>
            </div>
            <div class="flex-1">
                <input type="text" name="channel_name" placeholder="e.g., Main Fanpage, Support Channel"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500"
                       required>
            </div>
            <div>
                <button type="submit" class="btn-primary px-6 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-plus"></i>
                    <span>Add Channel</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Channels List -->
    <div class="space-y-4">
        @forelse($bot->channels as $channel)
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" x-data="{ showConfig: false }">
            <form action="{{ route('bots.channels.update', [$bot, $channel]) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="channel_type" value="{{ $channel->channel_type }}">

                <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-700/50 dark:to-gray-800/50 border-b border-gray-200 dark:border-gray-700 flex flex-wrap items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
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
                            <h4 class="font-bold text-gray-900 dark:text-white">{{ $channel->channel_name }}</h4>
                            <p class="text-xs text-gray-500 dark:text-gray-400">ID: {{ $channel->id }} • {{ ucfirst($channel->channel_type) }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-4">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" {{ $channel->is_active ? 'checked' : '' }} class="w-4 h-4 text-blue-600 rounded focus:ring-blue-500">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Active</span>
                        </label>
                        <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm font-medium">Save</button>
                        <button type="button" @click="showConfig = !showConfig" class="text-gray-500 hover:text-gray-700">
                            <i class="fas" :class="showConfig ? 'fa-chevron-up' : 'fa-chevron-down'"></i>
                        </button>
                    </div>
                </div>

                <div x-show="showConfig" x-collapse class="p-6 border-t border-gray-200 dark:border-gray-700 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Channel Name</label>
                        <input type="text" name="channel_name" value="{{ $channel->channel_name }}"
                               class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Webhook URL</label>
                        <div class="flex gap-2">
                            <input type="text" id="webhook-{{ $channel->id }}" readonly
                                   value="{{ $channel->getWebhookUrl() }}"
                                   class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg font-mono text-sm">
                            <button type="button" onclick="copyWebhook('webhook-{{ $channel->id }}', this)"
                                    class="px-4 py-2 bg-gray-200 dark:bg-gray-600 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition">
                                <i class="fas fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    @switch($channel->channel_type)
                        @case('fb')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Verify Token</label>
                                <input type="text" name="fb_verify_token" value="{{ $channel->config['fb_verify_token'] ?? '' }}"
                                       class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Page Access Token</label>
                                <textarea name="fb_page_token" rows="3"
                                          class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ $channel->config['fb_page_token'] ?? '' }}</textarea>
                            </div>
                            @break

                        @case('zalo')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">OA Access Token</label>
                                <textarea name="zalo_access_token" rows="3"
                                          class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ $channel->config['zalo_access_token'] ?? '' }}</textarea>
                            </div>
                            @break

                        @case('wa')
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Verify Token</label>
                                    <input type="text" name="whatsapp_verify_token" value="{{ $channel->config['whatsapp_verify_token'] ?? '' }}"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Phone Number ID</label>
                                    <input type="text" name="whatsapp_phone_number_id" value="{{ $channel->config['whatsapp_phone_number_id'] ?? '' }}"
                                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">System User Token</label>
                                <textarea name="whatsapp_token" rows="2"
                                          class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ $channel->config['whatsapp_token'] ?? '' }}</textarea>
                            </div>
                            @break

                        @case('sp')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Shop ID</label>
                                <input type="text" name="shopee_shop_id" value="{{ $channel->config['shopee_shop_id'] ?? '' }}"
                                       class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Access Token</label>
                                <textarea name="shopee_access_token" rows="2"
                                          class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ $channel->config['shopee_access_token'] ?? '' }}</textarea>
                            </div>
                            @break

                        @case('tt')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Access Token</label>
                                <textarea name="tiktok_access_token" rows="3"
                                          class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ $channel->config['tiktok_access_token'] ?? '' }}</textarea>
                            </div>
                            @break

                        @case('zlpn')
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">API Key / Token</label>
                                <textarea name="zalo_personal_token" rows="3"
                                          class="w-full max-w-md px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ $channel->config['zalo_personal_token'] ?? '' }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Format: API_URL|TOKEN or just TOKEN</p>
                            </div>
                            @break
                    @endswitch
                </div>
            </form>

            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/30 border-t border-gray-200 dark:border-gray-700 flex justify-end">
                <form action="{{ route('bots.channels.destroy', [$bot, $channel]) }}" method="POST" onsubmit="return confirmDeleteChannel(this)">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="text-red-600 hover:text-red-800 text-sm font-medium flex items-center gap-1">
                        <i class="fas fa-trash-alt"></i>
                        <span>Delete Channel</span>
                    </button>
                </form>
            </div>
        </div>
        @empty
        <div class="bg-white dark:bg-gray-800 rounded-2xl p-12 text-center shadow-lg">
            <div class="text-6xl mb-4 opacity-50">📱</div>
            <h3 class="text-xl font-bold text-gray-900 dark:text-white mb-2">No Channels Connected</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-6">Connect your bot to social platforms to reach more customers</p>
            <button onclick="document.querySelector('select[name=\"channel_type\"]').focus()"
                    class="btn-primary px-6 py-3 rounded-xl text-white font-semibold inline-flex items-center gap-2">
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
}

function confirmDeleteChannel(form) {
    Swal.fire({
        title: 'Delete Channel?',
        text: 'This channel configuration will be permanently removed.',
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
