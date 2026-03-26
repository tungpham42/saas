<?php

namespace App\Http\Controllers;

use App\Models\Bot;
use App\Models\RAGDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use ZipArchive;

class RAGController extends Controller
{
    public function index(Bot $bot)
    {
        $this->authorizeBot($bot);

        // Use query builder directly to avoid model relationship issues
        $documents = \DB::table('rag_documents')
            ->where('bot_id', $bot->id)
            ->orderBy('id', 'desc')
            ->get();

        return view('bots.tabs.rag', compact('bot', 'documents'));
    }

    public function store(Request $request, Bot $bot)
    {
        $this->authorizeBot($bot);

        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'gdrive_url' => 'nullable|url',
            'json_endpoint_url' => 'nullable|url',
            'rag_file' => 'nullable|file|mimes:txt,pdf,docx,xlsx,pptx,csv|max:20480',
        ]);

        $content = '';
        $sourceType = '';
        $title = $validated['title'] ?? '';

        // Process Google Drive
        if (!empty($validated['gdrive_url'])) {
            $content = $this->extractFromGoogleDrive($validated['gdrive_url']);
            $sourceType = 'google_drive';
            if (empty($title)) $title = 'Google Drive Document';
        }
        // Process JSON Endpoint
        elseif (!empty($validated['json_endpoint_url'])) {
            $content = $this->extractFromJsonEndpoint($validated['json_endpoint_url']);
            $sourceType = 'json_realtime';
            if (empty($title)) {
                $parsedUrl = parse_url($validated['json_endpoint_url']);
                $title = 'JSON API: ' . ($parsedUrl['host'] ?? 'endpoint');
            }
        }
        // Process File Upload
        elseif ($request->hasFile('rag_file')) {
            $file = $request->file('rag_file');
            $extension = $file->getClientOriginalExtension();
            $content = $this->extractFromFile($file->getPathname(), $extension);
            $sourceType = 'uploaded_file';
            if (empty($title)) $title = $file->getClientOriginalName();
        }

        if (empty($content)) {
            return redirect()->back()->with('error', 'Could not extract content from the provided source.');
        }

        // Truncate content if too long
        $content = mb_substr($content, 0, 100000);

        // Insert directly using DB facade to avoid model issues
        \DB::table('rag_documents')->insert([
            'bot_id' => $bot->id,
            'title' => $title,
            'source_type' => $sourceType,
            'content' => $content,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Document added to knowledge base successfully.');
    }

    public function destroy(Bot $bot, $ragDocumentId)
    {
        $this->authorizeBot($bot);

        // Delete directly using DB facade
        $deleted = \DB::table('rag_documents')
            ->where('id', $ragDocumentId)
            ->where('bot_id', $bot->id)
            ->delete();

        if ($deleted) {
            return redirect()->back()->with('success', 'Document deleted successfully.');
        }

        return redirect()->back()->with('error', 'Document not found.');
    }

    private function extractFromGoogleDrive(string $url): string
    {
        $content = '';

        if (preg_match('/docs\.google\.com\/(document|spreadsheets|presentation)\/d\/([a-zA-Z0-9-_]+)/', $url, $matches)) {
            $type = $matches[1];
            $id = $matches[2];
            $exportUrl = '';

            if ($type === 'document') {
                $exportUrl = "https://docs.google.com/document/d/{$id}/export?format=txt";
            } elseif ($type === 'spreadsheets') {
                $exportUrl = "https://docs.google.com/spreadsheets/d/{$id}/export?format=csv";
            } elseif ($type === 'presentation') {
                $exportUrl = "https://docs.google.com/presentation/d/{$id}/export/txt";
            }

            if ($exportUrl) {
                try {
                    $response = Http::timeout(30)->get($exportUrl);
                    if ($response->successful()) {
                        $content = $response->body();
                        if ($type === 'spreadsheets' || $type === 'presentation') {
                            $content = strip_tags($content);
                        }
                    }
                } catch (\Exception $e) {
                    // Log error but continue
                }
            }
        }

        return mb_substr(trim($content), 0, 100000);
    }

    private function extractFromJsonEndpoint(string $url): string
    {
        try {
            $response = Http::timeout(30)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (is_array($data)) {
                    return mb_substr(trim(json_encode($data, JSON_UNESCAPED_UNICODE)), 0, 100000);
                }
            }
        } catch (\Exception $e) {
            // Log error but continue
        }

        return '';
    }

    private function extractFromFile(string $path, string $extension): string
    {
        $extension = strtolower($extension);
        $content = '';

        if (in_array($extension, ['txt', 'csv'])) {
            $content = file_get_contents($path);
        } elseif ($extension === 'pdf') {
            $content = $this->extractPdfText($path);
        } elseif (in_array($extension, ['docx', 'xlsx', 'pptx']) && class_exists('ZipArchive')) {
            $content = $this->extractOfficeText($path, $extension);
        }

        return mb_substr(trim($content), 0, 100000);
    }

    private function extractPdfText(string $path): string
    {
        // Try using PDF parser if available
        if (class_exists('\Smalot\PdfParser\Parser')) {
            try {
                $parser = new \Smalot\PdfParser\Parser();
                $pdf = $parser->parseFile($path);
                return $pdf->getText();
            } catch (\Exception $e) {
                // Fallback to simple extraction
            }
        }

        // Fallback: extract text from PDF streams
        $content = file_get_contents($path);
        $text = '';

        if (preg_match_all('/stream[\r\n]+(.*?)[\r\n]+endstream/s', $content, $matches)) {
            foreach ($matches[1] as $stream) {
                $decoded = @gzuncompress($stream);
                if ($decoded !== false && preg_match_all('/\((.*?)\)/s', $decoded, $texts)) {
                    $text .= implode(' ', $texts[1]) . ' ';
                }
            }
        }

        $text = str_replace(['\\(', '\\)', '\\n', '\\r', '\\t'], ['(', ')', "\n", " ", " "], $text);
        return trim(preg_replace('/\s+/', ' ', preg_replace('/[^\p{L}\p{N}\s\.,\-\?!()]/u', '', $text)));
    }

    private function extractOfficeText(string $path, string $extension): string
    {
        $content = '';
        $zip = new ZipArchive();

        if ($zip->open($path) === true) {
            for ($i = 0; $i < $zip->numFiles; $i++) {
                $name = $zip->getNameIndex($i);

                if ($extension === 'docx' && $name === 'word/document.xml') {
                    $content .= strip_tags(str_replace(['<w:p>', '</w:p>'], [" ", "\n"], $zip->getFromIndex($i)));
                }
                if ($extension === 'xlsx' && $name === 'xl/sharedStrings.xml') {
                    $content .= strip_tags(str_replace(['<t>', '</t>'], [" ", " "], $zip->getFromIndex($i)));
                }
                if ($extension === 'pptx' && strpos($name, 'ppt/slides/slide') !== false) {
                    $content .= strip_tags(str_replace(['<a:t>', '</a:t>'], [" ", "\n"], $zip->getFromIndex($i))) . "\n";
                }
            }
            $zip->close();
        }

        return $content;
    }

    private function authorizeBot(Bot $bot)
    {
        $user = auth()->user();

        if (!$user->isAdmin() && $bot->user_id !== $user->id) {
            abort(403, 'You do not own this bot.');
        }
    }
}
