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
    Schema::create('wa_sessions', function (Blueprint $table) {
        $table->id();
        $table->string('phone')->unique();
        $table->string('step')->default('awaiting_subject');
        $table->json('data')->nullable(); // untuk simpan input sementara
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('w_a_sessions');
    }
};
