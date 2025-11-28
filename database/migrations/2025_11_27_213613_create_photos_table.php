<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('photos', function (Blueprint $table) {
            $table->id();

            // Storage info
            $table->string('disk')->default('public');
            $table->string('path');                // uploads/foo.jpg
            $table->string('file_name');           // original client name
            $table->string('mime_type')->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();

            // Basic image metadata
            $table->unsignedInteger('width')->nullable();
            $table->unsignedInteger('height')->nullable();

            // EXIF and any extra metadata as JSON
            $table->json('exif')->nullable();

            // TensorFLow / CNN required fields
            //    { "image": "134_3127.jpg", "style": “6in 107v", "installationStatus": "Complete” , “confidence”:”.98”} , 
            //
            $table->string('image');
            $table->string('product');
            $table->string('size');
            $table->string('type')->nullable();
            $table->string('installationStatus');
            $table->string('confidence');
            $table->decimal('score', 5, 2);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('photos');
    }
};
