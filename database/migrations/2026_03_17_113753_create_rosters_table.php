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
        Schema::create('rosters', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();
            $table->foreignId('clan_id')->constrained()->cascadeOnDelete();

            $table->string('name', 100);
            $table->string('description', 255)->nullable();

            $table->string('faction', 20);

            $table->unsignedInteger('max_soldiers')->default(0);

            $table->foreignId('map_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('central_point_id')->nullable()->constrained()->nullOnDelete();

            $table->string('image')->nullable();

            $table->boolean('is_public')->default(false);
            $table->boolean('multiclan')->default(false);
            $table->boolean('multifaction')->default(false);

            $table->timestamps();

            $table->unique(['clan_id', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rosters');
    }
};
