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
        Schema::table('photos', function (Blueprint $table) {
            // Change confidence to decimal
            $table->decimal('confidence', 5, 2)->nullable()->change();

            // Remove score
            $table->dropColumn('score');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            // Revert confidence — assuming it was string before
            $table->string('confidence')->nullable()->change();

            // Restore score — assuming it was decimal (adjust if needed)
            $table->decimal('score', 5, 2)->nullable();
        });

    }
};
