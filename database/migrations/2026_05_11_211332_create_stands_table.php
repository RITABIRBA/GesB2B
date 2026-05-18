<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_evenement')->constrained('evenements')->onDelete('cascade');
            $table->foreignId('id_entreprise')->constrained('entreprises')->onDelete('cascade');
            $table->integer('numero_stand');
            $table->decimal('superficie', 8, 2);
            $table->enum('standing', ['standard', 'premium', 'vip'])->default('standard');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('stands');
    }
};