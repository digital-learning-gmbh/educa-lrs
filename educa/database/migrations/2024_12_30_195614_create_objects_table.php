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
        Schema::create('learning_objects', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Name of the object (e.g., "Course Module 1")
            $table->string('type'); // Object type (e.g., "activity", "course")
            $table->string('iri')->unique(); // Unique IRI (Internationalized Resource Identifier)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_objects');
    }
};
