<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->integer('sla_response_time')->nullable()->after('name'); // dalam menit
            $table->integer('sla_resolution_time')->nullable()->after('sla_response_time'); // dalam menit
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['sla_response_time', 'sla_resolution_time']);
        });
    }
};
