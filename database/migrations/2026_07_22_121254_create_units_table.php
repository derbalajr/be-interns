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
        Schema::create('units', function (Blueprint $table) {
            $table->id();

            $table->string('code');
            $table->string('type');

            $table->decimal('area', 10, 2);
            $table->decimal('price', 15, 2);

            $table->string('status')->default('available');

            $table->foreignId('project_id')
                ->constrained()
                ->restrictOnDelete();

            $table->timestamps();

            $table->unique(['project_id', 'code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('units');
    }
};
