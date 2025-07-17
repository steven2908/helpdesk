<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
    $table->id();
    $table->string('ticket_id')->unique();
    $table->string('subject');
    $table->text('message');
    $table->enum('status', ['open', 'answered', 'closed'])->default('open');
    $table->foreignId('user_id')->constrained()->onDelete('cascade'); // pembuat
    $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null'); // staf
    $table->timestamps();
});



    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
