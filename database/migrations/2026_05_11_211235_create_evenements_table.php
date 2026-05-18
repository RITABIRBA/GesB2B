<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('evenements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_type_evenement')->constrained('type_evenements')->onDelete('cascade');
            $table->string('nom');
            $table->integer('annee');
            $table->date('date_debut');
            $table->date('date_fin');
            $table->time('heure_debut');
            $table->time('heure_fin');
            $table->string('ville');
            $table->string('lieu');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('evenements');
    }
};