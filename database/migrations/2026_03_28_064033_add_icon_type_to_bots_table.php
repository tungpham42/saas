<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('bots', function (Blueprint $table) {
            // Add icon_type to track which radio button is active (default to 'emoji' so existing bots don't break)
            $table->string('icon_type')->default('emoji')->after('ui_pos_left');

            // Make the original emoji field nullable so it can be empty when using a custom image
            $table->string('ui_trigger_icon')->nullable()->change();
        });
    }

    public function down()
    {
        Schema::table('bots', function (Blueprint $table) {
            // Drop the added columns
            $table->dropColumn(['icon_type', 'ui_trigger_custom_icon']);

            // Revert ui_trigger_icon to be not nullable (assuming its original state required a string)
            // If it had a default value previously, you might want to chain ->default('💬') here.
            $table->string('ui_trigger_icon')->nullable(false)->change();
        });
    }
};
