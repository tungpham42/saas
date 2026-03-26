<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('bot_id')->constrained()->onDelete('cascade');
            $table->string('session_id');
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->timestamps();

            $table->index(['bot_id', 'session_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
