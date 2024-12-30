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
        Schema::create('statements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actor_id')->constrained('actors')->onDelete('cascade');
            $table->foreignId('verb_id')->constrained('verbs')->onDelete('cascade');
            $table->foreignId('object_id')->constrained('learning_objects')->onDelete('cascade');
            $table->json('result')->nullable(); // To store result properties
            $table->json('context')->nullable(); // To store context properties
            $table->timestamp('timestamp')->nullable(); // Timestamp for the action
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('statements');
    }
};
