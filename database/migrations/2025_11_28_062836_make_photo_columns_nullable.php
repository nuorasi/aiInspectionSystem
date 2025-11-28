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
            $table->string('product')->nullable()->change();
            $table->string('size')->nullable()->change();
            $table->string('type')->nullable()->change();
            $table->string('installationStatus')->nullable()->change();
            $table->decimal('confidence', 5, 2)->nullable()->change(); // adjust decimals if needed
                 // adjust decimals if needed
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('photos', function (Blueprint $table) {
            $table->string('product')->nullable(false)->change();
            $table->string('size')->nullable(false)->change();
            $table->string('type')->nullable(false)->change();
            $table->string('installationStatus')->nullable(false)->change();
            $table->decimal('confidence', 5, 2)->nullable(false)->change();

        });
    }
};
