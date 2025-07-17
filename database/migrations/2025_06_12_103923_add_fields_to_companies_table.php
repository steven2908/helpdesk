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
    Schema::table('companies', function (Blueprint $table) {
        $table->string('address')->nullable();
        $table->string('phone')->nullable();
        $table->string('email')->nullable();
    });
}

public function down()
{
    Schema::table('companies', function (Blueprint $table) {
        $table->dropColumn(['address', 'phone', 'email']);
    });
}

};
