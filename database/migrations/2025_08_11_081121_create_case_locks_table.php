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
        Schema::create('case_locks', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('technician_name');
            $table->string('title');
            $table->text('reason');
            $table->text('impact');
            $table->text('notes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('case_locks');
    }
};
