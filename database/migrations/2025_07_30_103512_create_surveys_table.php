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
    Schema::create('surveys', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade');
        $table->unsignedTinyInteger('q1'); // Responsivitas Tim
        $table->unsignedTinyInteger('q2'); // Komunikasi & Koordinasi
        $table->unsignedTinyInteger('q3'); // Sikap & Keramahan Tim
        $table->unsignedTinyInteger('q4'); // Pengetahuan Teknis
        $table->unsignedTinyInteger('q5'); // Kepuasan Keseluruhan
        $table->text('saran')->nullable(); // Saran tambahan
        $table->timestamps();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('surveys');
    }
};
