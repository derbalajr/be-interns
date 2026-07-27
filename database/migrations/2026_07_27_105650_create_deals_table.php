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
        Schema::create('deals', function (Blueprint $table) {
            $table->id();

            // Foreign key to leads table
            $table->foreignId('lead_id')
                ->constrained()
                ->cascadeOnDelete();

            // Optional unit (BIGINT as requested)
            $table->unsignedBigInteger('unit_id')->nullable();

            // Foreign key to users table (agent)
            $table->foreignId('agent_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Deal stage
            $table->string('stage')->default('new');

            // Deal value
            $table->decimal('value', 12, 2);

            // Expected closing date
            $table->date('expected_close')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deals');
    }
};
