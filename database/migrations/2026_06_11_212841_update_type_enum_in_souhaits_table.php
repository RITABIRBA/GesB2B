<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE souhaits MODIFY type ENUM(
            'envoye',
            'recu',
            'mutuel'
        ) DEFAULT 'envoye'");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE souhaits MODIFY type ENUM(
            'envoye',
            'recu'
        ) DEFAULT 'envoye'");
    }
};