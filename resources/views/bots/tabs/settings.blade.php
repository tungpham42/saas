<div x-data="settingsManager()" class="space-y-6">
    <!-- AI Settings Card -->
    <div class="card-warm overflow-hidden animate-gentle">
        <div class="gradient-warm px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-brain text-amber-900 text-xl"></i>
                <h3 class="text-amber-900 font-bold text-lg">AI Personality</h3>
            </div>
            <p class="text-amber-800/70 text-sm mt-1">Configure your bot's brain and behavior</p>
        </div>

        <form action="{{ route('bots.update', $bot) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-cloud-upload-alt mr-2 text-amber-500"></i>AI Provider
                    </label>
                    <select name="provider" x-model="provider" class="input-warm w-full">
                        <option value="openai" {{ $bot->provider == 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                        <option value="groq" {{ $bot->provider == 'groq' ? 'selected' : '' }}>Groq (Fast, Free tier)</option>
                        <option value="gemini" {{ $bot->provider == 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-key mr-2 text-amber-500"></i>API Key
                    </label>
                    <input type="password" name="provider_api_key"
                           value="{{ $bot->provider_api_key }}"
                           class="input-warm w-full"
                           placeholder="Enter your API key">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-cube mr-2 text-amber-500"></i>Model Name
                    </label>
                    <input type="text" name="model"
                           value="{{ $bot->model }}"
                           class="input-warm w-full"
                           placeholder="gpt-4o-mini">
                    <div class="mt-2 p-3 bg-amber-50 dark:bg-gray-800 rounded-xl">
                        <p class="text-xs text-amber-600 dark:text-amber-400 font-mono">
                            <i class="fas fa-lightbulb text-amber-500 mr-1"></i>
                            Recommended: <strong>OpenAI:</strong> gpt-4o-mini | <strong>Groq:</strong> llama-3.1-8b-instant | <strong>Gemini:</strong> gemini-1.5-flash
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-thermometer-half mr-2 text-amber-500"></i>Creativity (Temperature)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" step="0.1" min="0" max="2" name="temperature" x-model="temperature"
                               class="flex-1 h-2 bg-amber-200 rounded-lg appearance-none cursor-pointer">
                        <span class="text-sm font-mono bg-amber-100 dark:bg-gray-800 px-3 py-1 rounded-xl text-amber-700" x-text="temperature">{{ $bot->temperature ?? 0.5 }}</span>
                    </div>
                    <p class="text-xs text-amber-500 mt-1">0 = Strict & Focused • 0.7 = Balanced • 1.0+ = Creative & Playful</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                        <i class="fas fa-text-height mr-2 text-amber-500"></i>Response Length
                    </label>
                    <input type="number" name="max_tokens"
                           value="{{ $bot->max_tokens ?? 1024 }}"
                           class="input-warm w-full">
                    <p class="text-xs text-amber-500 mt-1">1000 tokens ≈ 750 words - Keep it cozy!</p>
                </div>
            </div>

            <div class="border-t border-amber-100 dark:border-gray-700 pt-6">
                <h4 class="text-lg font-bold text-amber-800 dark:text-amber-200 mb-4">Bot's Personality ✨</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">Who is your bot?</label>
                        <textarea name="prompt_persona" rows="3"
                                  class="input-warm w-full" placeholder="e.g., You are a friendly customer support agent who loves helping people...">{{ $bot->prompt_persona }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">What should it do?</label>
                        <textarea name="prompt_task" rows="3"
                                  class="input-warm w-full" placeholder="e.g., Answer questions based on the knowledge base, be helpful and concise...">{{ $bot->prompt_task }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">Extra Context</label>
                            <textarea name="prompt_context" rows="3"
                                      class="input-warm w-full">{{ $bot->prompt_context }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">How to speak?</label>
                            <textarea name="prompt_format" rows="3"
                                      class="input-warm w-full" placeholder="e.g., Use warm, friendly language. Keep responses short and helpful.">{{ $bot->prompt_format }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-soft inline-flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Save & Love ❤️</span>
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
    }
}
</script>
@endpush
