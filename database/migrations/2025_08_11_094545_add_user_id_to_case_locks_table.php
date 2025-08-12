<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
{
    Schema::table('case_locks', function (Blueprint $table) {
        if (!Schema::hasColumn('case_locks', 'user_id')) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
        } else {
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        }
    });
}


public function down()
{
    Schema::table('case_locks', function (Blueprint $table) {
        $table->dropConstrainedForeignId('user_id');
    });
}

};
