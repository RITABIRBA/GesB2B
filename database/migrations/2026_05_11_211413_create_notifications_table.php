<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_participant')->constrained('participants')->onDelete('cascade');
            $table->text('contenu');
            $table->date('date_envoie');
            $table->enum('type', ['email', 'sms', 'systeme'])->default('systeme');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};