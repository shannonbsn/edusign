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
        Schema::create('cours', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->foreignId('classe_id')->constrained('classes')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('intervenant_id')->constrained('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreignId('salle')->constrained('salles')->onUpdate('cascade')->onDelete('cascade');
            $table->dateTime('date');
            $table->dateTime('h_debut');
            $table->dateTime('h_fin');
            $table->integer('duree');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cours');
    }
};
