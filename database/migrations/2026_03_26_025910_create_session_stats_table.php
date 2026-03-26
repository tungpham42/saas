<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('session_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained()->onDelete('cascade');
            $table->string('session_id')->unique();
            $table->timestamp('start_time')->useCurrent();
            $table->timestamp('first_admin_time')->nullable();
            $table->timestamp('last_admin_time')->nullable();
            $table->integer('admin_msg_count')->default(0);
            $table->boolean('is_emailed')->default(false);
            $table->timestamps();

            $table->index(['bot_id', 'start_time']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('session_stats');
    }
};
