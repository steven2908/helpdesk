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
    Schema::table('wa_sessions', function (Blueprint $table) {
        $table->string('survey_type')->nullable()->after('data');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wa_sessions', function (Blueprint $table) {
            //
        });
    }
};
