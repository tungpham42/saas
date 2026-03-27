<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCustomIconToBotsTable extends Migration
{
    public function up()
    {
        Schema::table('bots', function (Blueprint $table) {
            $table->string('ui_trigger_custom_icon')->nullable()->after('ui_trigger_icon');
        });
    }

    public function down()
    {
        Schema::table('bots', function (Blueprint $table) {
            $table->dropColumn('ui_trigger_custom_icon');
        });
    }
}
