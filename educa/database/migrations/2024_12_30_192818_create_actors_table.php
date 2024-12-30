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
        Schema::create('actors', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Full name of the actor
            $table->string('mbox')->unique(); // Email address (mailto: format)
            $table->string('mbox_sha1sum')->nullable(); // SHA-1 hash of email
            $table->string('openid')->nullable(); // OpenID identifier
            $table->string('account_homepage')->nullable(); // Account system homepage
            $table->string('account_name')->nullable(); // Account username
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actors');
    }
};
