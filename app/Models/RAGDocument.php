<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RAGDocument extends Model
{
    // Explicitly set the table name to avoid pluralization issues
    protected $table = 'rag_documents';

    protected $fillable = [
        'bot_id', 'title', 'source_type', 'content'
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function bot()
    {
        return $this->belongsTo(Bot::class);
    }

    public function getFormattedSize(): string
    {
        $length = strlen($this->content);
        if ($length < 1000) return $length . ' chars';
        if ($length < 1000000) return round($length / 1000, 1) . ' KB';
        return round($length / 1000000, 1) . ' MB';
    }

    public function getSourceTypeLabel(): string
    {
        return match($this->source_type) {
            'google_drive' => 'Google Drive',
            'uploaded_file' => 'Uploaded File',
            'json_realtime' => 'JSON API',
            default => ucfirst(str_replace('_', ' ', $this->source_type)),
        };
    }

    public function getSourceIcon(): string
    {
        return match($this->source_type) {
            'google_drive' => '📄',
            'uploaded_file' => '📁',
            'json_realtime' => '🔄',
            default => '📄',
        };
    }
}
