<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE participants MODIFY COLUMN role ENUM('participant','exposant','visiteur','organisateur','vip','representant') NOT NULL DEFAULT 'participant'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE participants MODIFY COLUMN role ENUM('participant','exposant','visiteur','organisateur','vip') NOT NULL DEFAULT 'participant'");
    }
};