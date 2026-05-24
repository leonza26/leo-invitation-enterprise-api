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
        Schema::create('invitations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
            $table->string('slug')->unique();
            $table->string('title');
            $table->string('groom_name');
            $table->string('bride_name');
            $table->timestamp('wedding_date')->nullable();
            $table->string('wedding_location')->nullable();
            $table->json('theme_config')->nullable();
            $table->string('status')->default('draft'); // draft, active, suspended
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invitations');
    }
};
