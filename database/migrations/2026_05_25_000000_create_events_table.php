<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained('users')->onDelete('cascade');
            $table->string('event_name');
            $table->string('slug')->unique();
            $table->string('event_type')->default('wedding');
            $table->string('theme')->default('luxury-gold');
            $table->string('venue')->nullable();
            $table->dateTime('event_date')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['draft', 'published', 'archived'])->default('draft');
            $table->string('package')->default('basic'); // basic, premium, luxury
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
