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
        Schema::table('clients', function (Blueprint $table) {
            $table->string('national_id')->nullable()->unique();
            $table->string('full_arabic_name')->nullable();
            $table->enum('marital_status', ['single', 'married'])->nullable();
            $table->string('job')->nullable();
            $table->date('expiry_date')->nullable();
            $table->date('birthdate')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn(['national_id', 'full_arabic_name', 'marital_status', 'job', 'expiry_date', 'birthdate']);
        });
    }
};
