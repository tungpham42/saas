<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    <!-- Upload Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up">
        <div class="gradient-primary px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-upload text-white"></i>
                <h3 class="text-white font-bold text-lg">Upload Knowledge</h3>
            </div>
            <p class="text-white/70 text-sm">Add documents to train your AI</p>
        </div>

        <form action="{{ route('bots.rag.store', $bot) }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    <i class="fas fa-heading mr-2 text-blue-500"></i>Document Title
                </label>
                <input type="text" name="title"
                       class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                       placeholder="Optional - will use filename if empty">
            </div>

            <div class="space-y-4">
                <!-- Google Drive Option -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 hover:border-blue-500 transition">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <i class="fab fa-google-drive text-red-500 text-xl"></i>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Google Drive URL (Public)</span>
                    </label>
                    <input type="url" name="gdrive_url"
                           class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="https://docs.google.com/document/d/...">
                </div>

                <div class="text-center text-gray-400 text-sm">— OR —</div>

                <!-- File Upload Option -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 hover:border-blue-500 transition">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-file-upload text-blue-500 text-xl"></i>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Upload File</span>
                    </label>
                    <input type="file" name="rag_file"
                           accept=".txt,.pdf,.docx,.xlsx,.pptx,.csv"
                           class="mt-2 w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-gray-500 mt-2">Supported: TXT, PDF, DOCX, XLSX, PPTX, CSV (Max 20MB)</p>
                </div>

                <div class="text-center text-gray-400 text-sm">— OR —</div>

                <!-- JSON Endpoint Option -->
                <div class="border-2 border-dashed border-gray-300 dark:border-gray-600 rounded-xl p-4 hover:border-blue-500 transition">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <i class="fas fa-code text-purple-500 text-xl"></i>
                        <span class="font-medium text-gray-700 dark:text-gray-300">Real-time JSON Endpoint</span>
                    </label>
                    <input type="url" name="json_endpoint_url"
                           class="mt-2 w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                           placeholder="https://api.domain.com/data">
                    <p class="text-xs text-gray-500 mt-2">Will fetch and store JSON data from this endpoint</p>
                </div>
            </div>

            <button type="submit" class="w-full btn-primary py-3 rounded-xl text-white font-semibold flex items-center justify-center gap-2">
                <i class="fas fa-cloud-upload-alt"></i>
                <span>Extract & Save Document</span>
            </button>
        </form>
    </div>

    <!-- Document Library -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s">
        <div class="gradient-secondary px-6 py-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-book text-white"></i>
                <h3 class="text-white font-bold text-lg">Document Library</h3>
            </div>
            <p class="text-white/70 text-sm">{{ $bot->ragDocuments()->count() }} document(s) in knowledge base</p>
        </div>

        <div class="divide-y divide-gray-200 dark:divide-gray-700 max-h-[500px] overflow-y-auto">
            @forelse($bot->ragDocuments as $doc)
            <div class="p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-2">
                            <span class="text-2xl">
                                @switch($doc->source_type)
                                    @case('google_drive') <i class="fab fa-google-drive text-red-500"></i> @break
                                    @case('json_realtime') <i class="fas fa-code text-purple-500"></i> @break
                                    @default <i class="fas fa-file-alt text-blue-500"></i>
                                @endswitch
                            </span>
                            <h4 class="font-semibold text-gray-900 dark:text-white">{{ $doc->title }}</h4>
                            <span class="text-xs px-2 py-1 bg-gray-100 dark:bg-gray-700 rounded-full text-gray-600 dark:text-gray-400">
                                {{ $doc->getSourceTypeLabel() }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 mt-2 text-xs text-gray-500 dark:text-gray-400">
                            <span><i class="fas fa-database mr-1"></i> {{ $doc->getFormattedSize() }}</span>
                            <span><i class="far fa-clock mr-1"></i> {{ $doc->created_at->diffForHumans() }}</span>
                        </div>
                        <p class="text-xs text-gray-400 dark:text-gray-500 mt-2 line-clamp-2">{{ Str::limit(strip_tags($doc->content), 120) }}</p>
                    </div>
                    <div class="ml-4">
                        <form action="{{ route('bots.rag.destroy', [$bot, $doc]) }}" method="POST" onsubmit="return confirmDeleteDoc(this)">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="text-red-500 hover:text-red-700 transition" title="Delete document">
                                <i class="fas fa-trash-alt"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @empty
            <div class="p-12 text-center">
                <div class="text-6xl mb-4 opacity-50">📚</div>
                <p class="text-gray-600 dark:text-gray-400">No documents uploaded yet.</p>
                <p class="text-sm text-gray-500 dark:text-gray-500 mt-1">Add documents to create a knowledge base for your AI.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDeleteDoc(form) {
    Swal.fire({
        title: 'Delete Document?',
        text: 'This document will be removed from the knowledge base.',
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
