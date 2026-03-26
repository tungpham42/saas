<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('channels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained()->onDelete('cascade');
            $table->string('channel_type'); // fb, zalo, tt, sp, zlpn, wa
            $table->string('channel_name');
            $table->boolean('is_active')->default(true);
            $table->json('config')->nullable();
            $table->timestamps();

            $table->index(['bot_id', 'channel_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('channels');
    }
};
