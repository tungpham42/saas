<div x-data="settingsManager()" class="space-y-6">
    <div class="card-warm overflow-hidden animate-gentle">
        <div class="gradient-warm px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-brain text-amber-900 text-xl"></i>
                <h3 class="text-amber-900 font-bold text-lg">{{ __('AI Personality') }}</h3>
            </div>
            <p class="text-amber-800/70 text-sm mt-1">{{ __('Configure your bot\'s brain and behavior') }}</p>
        </div>

        <form action="{{ route('bots.update', $bot) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-robot mr-2 text-amber-500"></i>{{ __('Bot Name') }}</label>
                    <input type="text" name="name"
                        value="{{ $bot->name }}"
                        class="input-warm w-full"
                        required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-cloud-upload-alt mr-2 text-amber-500"></i>{{ __('AI Provider') }}
                    </label>
                    <select name="provider" x-model="provider" class="input-warm w-full">
                        <option value="openai" {{ $bot->provider == 'openai' ? 'selected' : '' }}>{{ __('OpenAI (ChatGPT)') }}</option>
                        <option value="groq" {{ $bot->provider == 'groq' ? 'selected' : '' }}>{{ __('Groq (Fast, Free tier)') }}</option>
                        <option value="gemini" {{ $bot->provider == 'gemini' ? 'selected' : '' }}>{{ __('Google Gemini') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-key mr-2 text-amber-500"></i>{{ __('API Key') }}
                    </label>
                    <input type="password" name="provider_api_key"
                           value="{{ $bot->provider_api_key }}"
                           class="input-warm w-full"
                           placeholder="{{ __('Enter your API key') }}">
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-cube mr-2 text-amber-500"></i>{{ __('Model Name') }}
                    </label>
                    <input type="text" name="model"
                           value="{{ $bot->model }}"
                           class="input-warm w-full"
                           placeholder="gpt-4o-mini">
                    <div class="mt-2 p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-mono">
                            <i class="fas fa-lightbulb text-amber-500 mr-1"></i>
                            {{ __('Recommended') }}: <strong>OpenAI:</strong> gpt-4o-mini | <strong>Groq:</strong> llama-3.1-8b-instant | <strong>Gemini:</strong> gemini-1.5-flash
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-thermometer-half mr-2 text-amber-500"></i>{{ __('Creativity (Temperature)') }}
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" step="0.1" min="0" max="2" name="temperature" x-model="temperature"
                               class="flex-1 h-2 bg-amber-200 rounded-lg appearance-none cursor-pointer">
                        <span class="text-sm font-mono bg-amber-100 dark:bg-gray-800 px-3 py-1 rounded-xl text-amber-700" x-text="temperature">{{ $bot->temperature ?? 0.5 }}</span>
                    </div>
                    <p class="text-xs text-amber-500 mt-1">{{ __('0 = Strict & Focused • 0.7 = Balanced • 1.0+ = Creative & Playful') }}</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-text-height mr-2 text-amber-500"></i>{{ __('Response Length') }}
                    </label>
                    <input type="number" name="max_tokens" min="512" step="512"
                           value="{{ $bot->max_tokens ?? 1024 }}"
                           class="input-warm w-full">
                    <p class="text-xs text-amber-500 mt-1">{{ __('1024 tokens ≈ 750 words - Keep it cozy!') }}</p>
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4">{{ __('Bot\'s Personality ✨') }}</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Who is your bot?') }}</label>
                        <textarea name="prompt_persona" rows="3"
                                  class="input-warm w-full" placeholder="{{ __('e.g., You are a friendly customer support agent who loves helping people...') }}">{{ $bot->prompt_persona }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('What should it do?') }}</label>
                        <textarea name="prompt_task" rows="3"
                                  class="input-warm w-full" placeholder="{{ __('e.g., Answer questions based on the knowledge base, be helpful and concise...') }}">{{ $bot->prompt_task }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Extra Context') }}</label>
                            <textarea name="prompt_context" rows="3"
                                      class="input-warm w-full">{{ $bot->prompt_context }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('How to speak?') }}</label>
                            <textarea name="prompt_format" rows="3"
                                      class="input-warm w-full" placeholder="{{ __('e.g., Use warm, friendly language. Keep responses short and helpful.') }}">{{ $bot->prompt_format }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4">{{ __('Chat Widget UI 🎨') }}</h4>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-heading mr-2 text-amber-500"></i>{{ __('Title') }}
                        </label>
                        <input type="text" name="ui_title" value="{{ $bot->ui_title ?? 'AI Assistant' }}" class="input-warm w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-smile mr-2 text-amber-500"></i>{{ __('Welcome Message') }}
                        </label>
                        <input type="text" name="ui_welcome_msg" value="{{ $bot->ui_welcome_msg ?? 'Hello! How can I help you today?' }}" class="input-warm w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-edit mr-2 text-amber-500"></i>{{ __('Placeholder Text') }}
                        </label>
                        <input type="text" name="ui_placeholder" value="{{ $bot->ui_placeholder ?? 'Type a message...' }}" class="input-warm w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-paper-plane mr-2 text-amber-500"></i>{{ __('Button Text') }}
                        </label>
                        <input type="text" name="ui_btn_text" value="{{ $bot->ui_btn_text ?? 'Send' }}" class="input-warm w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-palette mr-2 text-amber-500"></i>{{ __('Primary Color') }}
                        </label>
                        <input type="color" name="ui_color" value="{{ $bot->ui_color ?? '#1677ff' }}" class="h-10 w-full rounded-lg border border-amber-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-fill-drip mr-2 text-amber-500"></i>{{ __('Background Color') }}
                        </label>
                        <input type="color" name="ui_bg_color" value="{{ $bot->ui_bg_color ?? '#FFFFFF' }}" class="h-10 w-full rounded-lg border border-amber-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-font mr-2 text-amber-500"></i>{{ __('Text Color') }}
                        </label>
                        <input type="color" name="ui_text_color" value="{{ $bot->ui_text_color ?? '#333333' }}" class="h-10 w-full rounded-lg border border-amber-200">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-arrows-alt mr-2 text-amber-500"></i>{{ __('Position (Bottom)') }}
                        </label>
                        <input type="text" name="ui_pos_bottom" value="{{ $bot->ui_pos_bottom ?? '20px' }}" class="input-warm w-full" placeholder="20px">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-arrows-alt-h mr-2 text-amber-500"></i>{{ __('Position (Right)') }}
                        </label>
                        <input type="text" name="ui_pos_right" value="{{ $bot->ui_pos_right ?? '20px' }}" class="input-warm w-full" placeholder="20px">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-arrows-alt-h mr-2 text-amber-500"></i>{{ __('Position (Left)') }}
                        </label>
                        <input type="text" name="ui_pos_left" value="{{ $bot->ui_pos_left ?? 'auto' }}" class="input-warm w-full" placeholder="auto">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-comment-dots mr-2 text-amber-500"></i>{{ __('Trigger Icon') }}
                        </label>

                        <div class="mb-3">
                            <div class="flex gap-3">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="icon_type" value="emoji" x-model="iconType" class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                    <span class="text-sm text-amber-700 dark:text-amber-300">{{ __('Emoji Icon') }}</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="radio" name="icon_type" value="custom" x-model="iconType" class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                                    <span class="text-sm text-amber-700 dark:text-amber-300">{{ __('Custom Image') }}</span>
                                </label>
                            </div>
                        </div>

                        <div x-show="iconType === 'emoji'" x-cloak>
                            <input type="text" name="ui_trigger_icon" value="{{ $bot->ui_trigger_icon ?? '💬' }}" class="input-warm w-full" maxlength="2" placeholder="💬">
                            <p class="text-xs text-amber-500 mt-1">{{ __('Enter an emoji (max 2 characters)') }}</p>
                        </div>

                        <div x-show="iconType === 'custom'" x-cloak>
                            <div class="space-y-3">
                                @if($bot->ui_trigger_custom_icon)
                                    <div class="flex items-center gap-3 p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                                        <img src="{{ asset($bot->ui_trigger_custom_icon) }}" alt="Current icon" class="w-12 h-12 object-contain rounded-lg border border-amber-200">
                                        <div class="flex-1">
                                            <p class="text-sm text-amber-700 dark:text-amber-300">{{ __('Current custom icon') }}</p>
                                            <label class="flex items-center gap-2 cursor-pointer mt-1">
                                                <input type="checkbox" name="remove_custom_icon" value="1" class="rounded border-amber-300 text-red-500 focus:ring-red-500">
                                                <span class="text-xs text-red-600 dark:text-red-400">{{ __('Remove this icon') }}</span>
                                            </label>
                                        </div>
                                    </div>
                                @endif

                                <div>
                                    <input type="file" name="ui_trigger_custom_icon" accept="image/png,image/jpg,image/jpeg,image/svg+xml,image/webp" class="input-warm w-full" @change="handleIconUpload">
                                    <p class="text-xs text-amber-500 mt-1">{{ __('Upload PNG, JPG, JPEG, SVG, WEBP (Max 1MB). Recommended size: 48x48px') }}</p>
                                </div>

                                <div x-show="iconPreview" x-cloak class="p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                                    <p class="text-xs text-amber-600 dark:text-amber-400 mb-2">{{ __('Preview:') }}</p>
                                    <img :src="iconPreview" class="w-12 h-12 object-contain rounded-lg border border-amber-200">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-border-all mr-2 text-amber-500"></i>{{ __('Trigger Border Radius') }}
                        </label>
                        <input type="text" name="ui_trigger_border_radius" value="{{ $bot->ui_trigger_border_radius ?? '50%' }}" class="input-warm w-full" placeholder="50%">
                        <p class="text-xs text-amber-500 mt-1">{{ __('Border radius for trigger button (e.g., 50% for circle, 12px for rounded square)') }}</p>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer w-fit">
                            <input type="hidden" name="ui_trigger_bg_transparent" value="0">
                            <input type="checkbox" name="ui_trigger_bg_transparent" value="1" {{ ($bot->ui_trigger_bg_transparent ?? false) ? 'checked' : '' }} class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                            <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                                <i class="fas fa-eye mr-2 text-amber-500"></i>{{ __('Transparent Trigger Background') }}
                            </span>
                        </label>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer w-fit">
                            <input type="hidden" name="ui_clear_on_close" value="0">
                            <input type="checkbox" name="ui_clear_on_close" value="1" {{ ($bot->ui_clear_on_close ?? false) ? 'checked' : '' }} class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                            <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                                <i class="fas fa-trash-alt mr-2 text-amber-500"></i>{{ __('Clear Chat on Close') }}
                            </span>
                        </label>
                    </div>

                    <div class="md:col-span-2">
                        <label class="flex items-center gap-3 cursor-pointer w-fit">
                            <input type="hidden" name="ui_pre_chat_form" value="0">
                            <input type="checkbox" name="ui_pre_chat_form" value="1" {{ ($bot->ui_pre_chat_form ?? false) ? 'checked' : '' }} class="rounded border-amber-300 text-amber-600 focus:ring-amber-500">
                            <span class="text-sm font-semibold text-amber-700 dark:text-amber-300">
                                <i class="fas fa-clipboard-list mr-2 text-amber-500"></i>{{ __('Enable Pre-Chat Form') }}
                            </span>
                        </label>
                    </div>
                </div>

                <div x-show="preChatForm" class="mt-4 p-4 rounded-xl space-y-4">
                    <h5 class="font-semibold text-amber-800 dark:text-amber-200">{{ __('Pre-Chat Form Configuration') }}</h5>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Form Message') }}</label>
                            <input type="text" name="ui_pre_chat_msg" value="{{ $bot->ui_pre_chat_msg ?? 'Please enter your information to start support:' }}" class="input-warm w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Name Label') }}</label>
                            <input type="text" name="ui_pre_chat_name_label" value="{{ $bot->ui_pre_chat_name_label ?? 'Full Name *' }}" class="input-warm w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Phone Label') }}</label>
                            <input type="text" name="ui_pre_chat_phone_label" value="{{ $bot->ui_pre_chat_phone_label ?? 'Phone Number *' }}" class="input-warm w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Button Text') }}</label>
                            <input type="text" name="ui_pre_chat_btn_text" value="{{ $bot->ui_pre_chat_btn_text ?? 'Start Chat' }}" class="input-warm w-full">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">{{ __('Error Message') }}</label>
                            <input type="text" name="ui_pre_chat_error_msg" value="{{ $bot->ui_pre_chat_error_msg ?? 'Please fill in all required information.' }}" class="input-warm w-full">
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4">{{ __('Admin & History ⚙️') }}</h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-clock mr-2 text-amber-500"></i>{{ __('Admin Timeout (minutes)') }}
                        </label>
                        <input type="number" name="admin_timeout_mins" min="1" step="1" value="{{ $bot->admin_timeout_mins ?? 15 }}" class="input-warm w-full">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-history mr-2 text-amber-500"></i>{{ __('History Limit') }}
                        </label>
                        <input type="number" name="history_limit" min="0" step="1" value="{{ $bot->history_limit ?? 5 }}" class="input-warm w-full">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-envelope mr-2 text-amber-500"></i>{{ __('Email Notification Addresses') }}
                        </label>
                        <input type="text" name="email_notify_addresses" value="{{ $bot->email_notify_addresses }}" class="input-warm w-full" placeholder="admin@example.com, support@example.com">
                        <p class="text-xs text-amber-500 mt-1">{{ __('Comma-separated email addresses') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                            <i class="fas fa-bell mr-2 text-amber-500"></i>{{ __('Email Notification Timeout (minutes)') }}
                        </label>
                        <input type="number" name="email_notify_timeout_mins" min="1" step="1" value="{{ $bot->email_notify_timeout_mins ?? 10 }}" class="input-warm w-full">
                    </div>
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4">{{ __('Embed Code & Preview 📋') }}</h4>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <div>
                        <p class="text-sm text-amber-600 dark:text-amber-400 mb-4">{{ __('Copy this code and paste it into your website:') }}</p>
                        <div class="bg-gray-900 dark:bg-gray-950 rounded-xl p-4">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-code text-amber-400"></i>
                                    <span class="text-xs text-gray-400">{{ __('JavaScript Embed Code') }}</span>
                                </div>
                                <button onclick="copyEmbedCode(); return false; " class="text-xs bg-gray-700 hover:bg-gray-600 text-white px-3 py-1 rounded transition">
                                    <i class="fas fa-copy mr-1"></i> {{ __('Copy') }}
                                </button>
                            </div>
                            <pre id="embed-code" class="text-xs text-gray-300 overflow-x-auto whitespace-pre-wrap font-mono"><code>&lt;!-- AI Chat Widget --&gt;
&lt;script src="{{ url('/') }}/api/saas/v1/embed.js?api_key={{ $bot->api_key }}" defer&gt;&lt;/script&gt;</code></pre>
                        </div>

                        <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl">
                            <p class="text-xs text-blue-600 dark:text-blue-400">
                                <i class="fas fa-info-circle mr-1"></i>
                                <strong>{{ __('Note:') }}</strong> {{ __('The widget will appear in the bottom corner of your website with your custom settings.') }}
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-amber-600 dark:text-amber-400 mb-4">{{ __('Live Preview (click to test):') }}</p>
                        <div class="bg-gray-100 dark:bg-gray-800 rounded-xl p-4 relative" style="min-height: 400px;">
                            <div class="text-center text-gray-500 dark:text-gray-400 text-sm mb-4">
                                <i class="fas fa-desktop mr-1"></i> {{ __('This is how your widget will look') }}
                            </div>
                            <div id="widget-preview-container" class="relative">
                                <div class="text-center text-gray-400 py-8">
                                    <i class="fas fa-spinner fa-spin text-2xl"></i>
                                    <p class="mt-2 text-xs">{{ __('Loading preview...') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-soft inline-flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>{{ __('Save & Love ❤️') }}</span>
                </button>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function settingsManager() {
    return {
        provider: '{{ $bot->provider }}',
        temperature: {{ $bot->temperature ?? 0.5 }},
        preChatForm: {{ $bot->ui_pre_chat_form ? 'true' : 'false' }},
        iconType: '{{ $bot->ui_trigger_custom_icon ? "custom" : "emoji" }}',
        iconPreview: null,

        handleIconUpload(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.iconPreview = e.target.result;
                };
                reader.readAsDataURL(file);
            } else {
                this.iconPreview = null;
            }
        }
    }
}

function copyEmbedCode() {
    const codeElement = document.getElementById('embed-code');
    const range = document.createRange();
    range.selectNode(codeElement);
    window.getSelection().removeAllRanges();
    window.getSelection().addRange(range);
    document.execCommand('copy');
    window.getSelection().removeAllRanges();

    // Show temporary notification
    const button = event.target.closest('button');
    const originalText = button.innerHTML;
    button.innerHTML = '<i class="fas fa-check mr-1"></i> {{ __('Copied!') }}';
    setTimeout(() => {
        button.innerHTML = originalText;
    }, 2000);
}

// Load widget preview
document.addEventListener('DOMContentLoaded', function() {
    const previewContainer = document.getElementById('widget-preview-container');
    if (previewContainer) {
        // Clear loading message
        previewContainer.innerHTML = '';

        // Create a temporary div for the widget
        const widgetDiv = document.createElement('div');
        widgetDiv.id = 'preview-widget';
        previewContainer.appendChild(widgetDiv);

        // Load the embed script
        const script = document.createElement('script');
        script.src = '{{ url("/") }}/api/saas/v1/embed.js?api_key={{ $bot->api_key }}';
        script.defer = true;
        script.onload = function() {
            console.log('Widget preview loaded');
        };
        script.onerror = function() {
            previewContainer.innerHTML = '<div class="text-center text-red-500 py-4"><i class="fas fa-exclamation-triangle"></i><p class="text-xs mt-2">{{ __('Failed to load widget preview. Make sure the embed endpoint is accessible.') }}</p></div>';
        };
        document.body.appendChild(script);
    }
});
</script>
@endpush
