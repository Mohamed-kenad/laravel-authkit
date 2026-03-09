<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authkit_teams', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('owner_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        Schema::create('authkit_team_user', function (Blueprint $table) {
            $table->foreignId('team_id')->constrained('authkit_teams')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('role')->nullable(); // role within the team
            $table->timestamps();

            $table->unique(['team_id', 'user_id']);
        });

        // Add current_team_id to users table if we want to track active team context
        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('current_team_id')->nullable()->constrained('authkit_teams')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['current_team_id']);
            $table->dropColumn('current_team_id');
        });

        Schema::dropIfExists('authkit_team_user');
        Schema::dropIfExists('authkit_teams');
    }
};
