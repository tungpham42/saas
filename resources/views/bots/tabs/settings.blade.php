<div x-data="settingsManager()" class="space-y-6">
    <!-- AI Settings Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up">
        <div class="gradient-primary px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-brain text-white text-xl"></i>
                <h3 class="text-white font-bold text-lg">AI Model Configuration</h3>
            </div>
            <p class="text-white/70 text-sm mt-1">Configure the brain of your chatbot</p>
        </div>

        <form action="{{ route('bots.update', $bot) }}" method="POST" class="p-6 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-cloud-upload-alt mr-2 text-blue-500"></i>AI Provider
                    </label>
                    <select name="provider" x-model="provider" class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        <option value="openai" {{ $bot->provider == 'openai' ? 'selected' : '' }}>OpenAI (ChatGPT)</option>
                        <option value="groq" {{ $bot->provider == 'groq' ? 'selected' : '' }}>Groq (Fast, Free tier)</option>
                        <option value="gemini" {{ $bot->provider == 'gemini' ? 'selected' : '' }}>Google Gemini</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-key mr-2 text-blue-500"></i>API Key
                    </label>
                    <input type="password" name="provider_api_key"
                           value="{{ $bot->provider_api_key }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="Enter your API key">
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-cube mr-2 text-blue-500"></i>Model Name
                    </label>
                    <input type="text" name="model"
                           value="{{ $bot->model }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="gpt-4o-mini">
                    <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <p class="text-xs text-gray-600 dark:text-gray-400 font-mono">
                            <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                            Recommended: <strong>OpenAI:</strong> gpt-4o-mini | <strong>Groq:</strong> llama-3.1-8b-instant | <strong>Gemini:</strong> gemini-1.5-flash
                        </p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-thermometer-half mr-2 text-blue-500"></i>Temperature (0.0 - 2.0)
                    </label>
                    <div class="flex items-center gap-3">
                        <input type="range" step="0.1" min="0" max="2" name="temperature" x-model="temperature"
                               class="flex-1 h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer">
                        <span class="text-sm font-mono bg-gray-100 dark:bg-gray-700 px-3 py-1 rounded-lg" x-text="temperature">{{ $bot->temperature ?? 0.5 }}</span>
                    </div>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">0 = Strict/Factual, 0.7 = Balanced, 1.0+ = Creative</p>
                </div>

                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                        <i class="fas fa-text-height mr-2 text-blue-500"></i>Max Tokens
                    </label>
                    <input type="number" name="max_tokens"
                           value="{{ $bot->max_tokens ?? 1024 }}"
                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">1000 tokens ≈ 750 words</p>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h4 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Prompt Engineering</h4>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">System Persona</label>
                        <textarea name="prompt_persona" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ $bot->prompt_persona }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Task Instructions</label>
                        <textarea name="prompt_task" rows="3"
                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ $bot->prompt_task }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Additional Context</label>
                            <textarea name="prompt_context" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ $bot->prompt_context }}</textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Output Format</label>
                            <textarea name="prompt_format" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ $bot->prompt_format }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end">
                <button type="submit" class="btn-primary px-8 py-3 rounded-xl text-white font-semibold flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Save All Settings</span>
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
