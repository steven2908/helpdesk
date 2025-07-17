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
    Schema::table('telegram_sessions', function (Blueprint $table) {
        $table->string('phone')->nullable()->after('chat_id');
    });
}

public function down()
{
    Schema::table('telegram_sessions', function (Blueprint $table) {
        $table->dropColumn('phone');
    });
}

};
