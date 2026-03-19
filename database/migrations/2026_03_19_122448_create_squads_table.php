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
        Schema::create('squads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('roster_id')->constrained()->cascadeOnDelete();
            $table->string('name', 100);
            $table->string('alias', 20);
            $table->string('roster_type_squad', 20);
            $table->unsignedInteger('pos_x')->default(0);
            $table->unsignedInteger('pos_y')->default(0);
            $table->unsignedInteger('z_index')->default(1);
            $table->timestamps();

            $table->unique(['roster_id', 'alias']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('squads');
    }
};
