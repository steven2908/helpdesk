<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open', 'in_progress', 'closed') DEFAULT 'open'");
}

public function down()
{
    DB::statement("ALTER TABLE tickets MODIFY COLUMN status ENUM('open', 'closed') DEFAULT 'open'");
}
};
