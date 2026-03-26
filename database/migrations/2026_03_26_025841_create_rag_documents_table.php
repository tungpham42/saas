<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rag_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained()->onDelete('cascade');
            $table->string('title');
            $table->string('source_type'); // google_drive, uploaded_file, json_realtime
            $table->longText('content');
            $table->timestamps();

            $table->index('bot_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rag_documents');
    }
};
