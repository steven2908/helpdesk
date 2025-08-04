<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->unsignedTinyInteger('cs_q1')->nullable()->after('q5');
            $table->unsignedTinyInteger('cs_q2')->nullable()->after('cs_q1');
            $table->unsignedTinyInteger('cs_q3')->nullable()->after('cs_q2');
        });
    }

    public function down(): void
    {
        Schema::table('surveys', function (Blueprint $table) {
            $table->dropColumn(['cs_q1', 'cs_q2', 'cs_q3']);
        });
    }
};
