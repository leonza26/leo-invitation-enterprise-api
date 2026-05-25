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
        Schema::table('guest_books', function (Blueprint $table) {
            $table->dropForeign(['invitation_id']);
            $table->dropColumn('invitation_id');
            $table->foreignId('event_id')->after('id')->constrained('events')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guest_books', function (Blueprint $table) {
            $table->dropForeign(['event_id']);
            $table->dropColumn('event_id');
            $table->foreignId('invitation_id')->after('id')->constrained('invitations')->cascadeOnDelete();
        });
    }
};
