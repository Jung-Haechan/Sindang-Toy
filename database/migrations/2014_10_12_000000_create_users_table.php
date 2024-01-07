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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('srl')->unique();
            $table->string('name');
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->string('region')->nullable();
            $table->unsignedInteger('age')->nullable();
            $table->boolean('is_admin')->default(false);
            $table->timestamp('registered_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
