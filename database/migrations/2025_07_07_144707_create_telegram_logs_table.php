<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('telegram_logs', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id')->nullable();
            $table->text('message')->nullable();
            $table->json('raw')->nullable(); // Menyimpan seluruh payload asli
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('telegram_logs');
    }
};
