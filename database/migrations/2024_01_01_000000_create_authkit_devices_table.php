<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authkit_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedBigInteger('token_id')->index(); // maps to personal_access_tokens.id
            $table->string('name');
            $table->string('platform')->default('unknown');
            $table->timestamp('last_active_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'token_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authkit_devices');
    }
};
