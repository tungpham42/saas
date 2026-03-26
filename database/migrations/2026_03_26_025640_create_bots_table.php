<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('api_key')->unique();
            $table->string('provider')->default('openai');
            $table->text('provider_api_key')->nullable();
            $table->string('model')->default('gpt-4o-mini');
            $table->float('temperature')->default(0.5);
            $table->integer('max_tokens')->default(1024);
            $table->text('prompt_persona')->nullable();
            $table->text('prompt_task')->nullable();
            $table->text('prompt_context')->nullable();
            $table->text('prompt_format')->nullable();
            $table->string('ui_title')->default('AI Assistant');
            $table->string('ui_welcome_msg')->default('Hello! How can I help you today?');
            $table->string('ui_placeholder')->default('Type a message...');
            $table->string('ui_btn_text')->default('Send');
            $table->string('ui_color')->default('#1677ff');
            $table->string('ui_bg_color')->default('#FFFFFF');
            $table->string('ui_text_color')->default('#333333');
            $table->string('ui_pos_bottom')->default('20px');
            $table->string('ui_pos_right')->default('20px');
            $table->string('ui_pos_left')->default('auto');
            $table->string('ui_trigger_icon')->default('💬');
            $table->boolean('ui_trigger_bg_transparent')->default(false);
            $table->string('ui_trigger_border_radius')->default('50%');
            $table->boolean('ui_clear_on_close')->default(false);
            $table->boolean('ui_pre_chat_form')->default(false);
            $table->string('ui_pre_chat_msg')->default('Please enter your information to start support:');
            $table->string('ui_pre_chat_name_label')->default('Full Name *');
            $table->string('ui_pre_chat_phone_label')->default('Phone Number *');
            $table->string('ui_pre_chat_btn_text')->default('Start Chat');
            $table->string('ui_pre_chat_error_msg')->default('Please fill in all required information.');
            $table->integer('admin_timeout_mins')->default(15);
            $table->integer('history_limit')->default(5);
            $table->text('email_notify_addresses')->nullable();
            $table->integer('email_notify_timeout_mins')->default(10);
            $table->timestamps();

            $table->index('api_key');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bots');
    }
};
