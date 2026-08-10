<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('unit_media', function (Blueprint $table) {
            $table->id();

            $table->foreignId('unit_id')
                ->constrained()
                ->cascadeOnDelete(); // tells us which unit owns the image

            $table->string('path'); // where the image is stored in the storage folder

            $table->string('type'); // the type of media (e.g., photo, floor plan)

            $table->timestamps(); // created_at and updated_at columns
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('unit_media');
    }
};
