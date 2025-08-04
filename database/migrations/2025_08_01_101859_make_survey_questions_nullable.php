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
    Schema::table('surveys', function (Blueprint $table) {
        $table->integer('q1')->nullable()->change();
        $table->integer('q2')->nullable()->change();
        $table->integer('q3')->nullable()->change();
        $table->integer('q4')->nullable()->change();
        $table->integer('q5')->nullable()->change();
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
