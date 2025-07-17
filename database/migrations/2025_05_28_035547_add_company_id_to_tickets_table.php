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
    Schema::table('tickets', function (Blueprint $table) {
        $table->foreignId('company_id')->nullable()->after('user_id');
        // Optional jika kamu punya tabel `companies`
        // $table->foreign('company_id')->references('id')->on('companies')->onDelete('set null');
    });
}

public function down(): void
{
    Schema::table('tickets', function (Blueprint $table) {
        $table->dropColumn('company_id');
    });
}

};
