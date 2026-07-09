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
        Schema::create('squad_soldiers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('squad_id')->constrained()->cascadeOnDelete();
            $table->foreignId('soldier_id')->nullable()->constrained()->nullOnDelete();
            $table->string('display_name', 32);
            $table->unsignedTinyInteger('slot_number');
            $table->string('role_squad_type', 50)->nullable();
            $table->timestamps();

            $table->unique(['squad_id', 'slot_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('squad_soldiers');
    }
};
