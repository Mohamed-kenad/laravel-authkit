<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('authkit_roles', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('authkit_permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->timestamps();
        });

        Schema::create('authkit_role_user', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('authkit_roles')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->primary(['role_id', 'user_id']);
        });

        Schema::create('authkit_permission_role', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained('authkit_permissions')->cascadeOnDelete();
            $table->foreignId('role_id')->constrained('authkit_roles')->cascadeOnDelete();
            $table->primary(['permission_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('authkit_permission_role');
        Schema::dropIfExists('authkit_role_user');
        Schema::dropIfExists('authkit_permissions');
        Schema::dropIfExists('authkit_roles');
    }
};
