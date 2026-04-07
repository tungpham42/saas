<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <div class="card-warm overflow-hidden animate-gentle">
        <div class="gradient-warm px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-brain text-amber-900 text-xl"></i>
                <h3 class="text-amber-900 font-bold text-lg">{{ __('Teach Your Bot 📚') }}</h3>
            </div>
            <p class="text-amber-800/70 text-sm mt-1">{{ __('Upload knowledge to make your AI smarter') }}</p>
        </div>

        <form action="{{ route('bots.rag.store', $bot) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-amber-700 dark:text-amber-300 mb-2">
                    <i class="fas fa-heading mr-2 text-amber-500"></i>{{ __('Document Title') }}
                </label>
                <input type="text" name="title"
                       class="input-warm w-full"
                       placeholder="{{ __('Give it a friendly name (optional)') }}">
            </div>

            <div class="space-y-4">
                <div class="border-2 border-dashed border-amber-200 dark:border-gray-600 rounded-2xl p-4 hover:border-amber-400 transition">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <i class="fab fa-google-drive text-red-500 text-xl"></i>
                        <span class="font-medium text-amber-700 dark:text-amber-300">{{ __('Google Drive URL') }}</span>
                    </label>
                    <input type="url" name="gdrive_url"
                           class="mt-2 input-warm w-full"
                           placeholder="https://docs.google.com/document/d/...">
                    <p class="text-xs text-amber-500 mt-1">{{ __('Make sure the document is publicly accessible') }}</p>
                </div>

                <div class="text-center text-amber-400 text-sm">🌸 — OR — 🌸</div>

                <div class="border-2 border-dashed border-amber-200 dark:border-gray-600 rounded-2xl p-4 hover:border-amber-400 transition">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-amber-500 text-xl"></i>
                        <span class="font-medium text-amber-700 dark:text-amber-300">{{ __('Upload a File') }}</span>
                    </label>
                    <input type="file" name="rag_file"
                           accept=".txt,.pdf,.docx,.xlsx,.pptx,.csv"
                           class="mt-2 w-full text-sm text-amber-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-amber-100 file:text-amber-700 hover:file:bg-amber-200">
                    <p class="text-xs text-amber-500 mt-2">{{ __('📄 PDF • 📝 DOCX • 📊 XLSX • 📽️ PPTX • 📃 TXT • 📈 CSV (Max 20MB)') }}</p>
                </div>

                <div class="text-center text-amber-400 text-sm">✨ — OR — ✨</div>

                <div class="border-2 border-dashed border-amber-200 dark:border-gray-600 rounded-2xl p-4 hover:border-amber-400 transition">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-code text-purple-500 text-xl"></i>
                        <span class="font-medium text-amber-700 dark:text-amber-300">{{ __('Real-time JSON Endpoint') }}</span>
                    </label>
                    <input type="url" name="json_endpoint_url"
                           class="mt-2 input-warm w-full"
                           placeholder="https://api.yourdomain.com/data">
                    <p class="text-xs text-amber-500 mt-2">{{ __('Will fetch fresh data from your API endpoint') }}</p>
                </div>
            </div>

            <button type="submit" class="w-full btn-soft py-3 rounded-xl text-amber-900 font-semibold flex items-center justify-center gap-2">
                <i class="fas fa-graduation-cap"></i>
                <span>{{ __('Teach My Bot 📚') }}</span>
            </button>
        </form>
    </div>

    <div class="card-warm overflow-hidden animate-gentle" style="animation-delay: 0.1s">
        <div class="gradient-warm px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-book-open text-amber-900 text-xl"></i>
                <h3 class="text-amber-900 font-bold text-lg">{{ __('Bot\'s Knowledge Base') }}</h3>
            </div>
            <p class="text-amber-800/70 text-sm mt-1">{{ $bot->ragDocuments()->count() }} {{ __('document(s) in memory') }}</p>
        </div>

        <div class="divide-y divide-amber-100 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
            @forelse($bot->ragDocuments as $doc)
            <div class="p-4 hover:bg-amber-50 dark:hover:bg-gray-800 transition-colors group">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-2xl">
                                @switch($doc->source_type)
                                    @case('google_drive') <i class="fab fa-google-drive text-red-500"></i> @break
                                    @case('json_realtime') <i class="fas fa-code text-purple-500"></i> @break
                                    @default <i class="fas fa-file-alt text-amber-500"></i>
                                @endswitch
                            </span>
                            <h4 class="font-semibold text-amber-800 dark:text-amber-200">{{ $doc->title }}</h4>
                            <span class="text-xs px-2 py-1 bg-amber-100 dark:bg-gray-700 rounded-full text-amber-600 dark:text-amber-400">
                                {{ $doc->getSourceTypeLabel() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mt-2 text-xs text-amber-500 dark:text-amber-400">
                            <span><i class="fas fa-database mr-1"></i> {{ $doc->getFormattedSize() }}</span>
                            <span><i class="far fa-clock mr-1"></i> {{ $doc->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-amber-400 dark:text-amber-500 mt-2 line-clamp-2">
                            {{ Str::limit(strip_tags($doc->content), 120) }}
                        </p>
                    </div>
                    <div class="ml-4 opacity-0 group-hover:opacity-100 transition">
                        <form action="{{ route('bots.rag.destroy', [$bot, $doc]) }}" method="POST" onsubmit="return confirmDeleteDoc(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-400 hover:text-red-500 transition" title="{{ __('Remove from knowledge base') }}">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="text-6xl mb-4">📖</div>
                <p class="text-amber-600 dark:text-amber-400">{{ __('No documents yet') }}</p>
                <p class="text-sm text-amber-500 dark:text-amber-500 mt-1">{{ __('Upload your first document to make your bot smarter!') }}</p>
                <div class="mt-4 text-amber-400 text-sm">
                    <i class="fas fa-heart"></i> {{ __('Your bot is eager to learn') }}
                </div>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDeleteDoc(form) {
    Swal.fire({
        title: '{{ __('Remove from Knowledge Base? 💔') }}',
        text: '{{ __('Your bot will no longer have access to this information.') }}',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#f59e0b',
        cancelButtonColor: '#6b7280',
        confirmButtonText: '{{ __('Yes, remove it') }}',
        cancelButtonText: '{{ __('Keep it') }}'
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
    return false;
}
</script>
@endpush
