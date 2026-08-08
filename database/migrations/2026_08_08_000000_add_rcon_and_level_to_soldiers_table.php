<?php

declare(strict_types=1);

namespace Database\migrations;

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add rcon and level columns to soldiers table after the role column
     */
    public function up(): void
    {
        Schema::table('soldiers', function (Blueprint $table) {
            $table->string('rcon', 255)->nullable()->after('role');
            $table->unsignedInteger('level')->default(1)->after('rcon');
        });
    }

    public function down(): void
    {
        /**
         * Remove rcon and level columns from soldiers table
         */
        Schema::table('soldiers', function (Blueprint $table) {
            $table->dropColumn(['rcon', 'level']);
        });
    }
};
