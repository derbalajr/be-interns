<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Widen reservations.status from an enum to a plain string so it can hold
     * the new "completed" state (reached when a unit's handover completes).
     * Matches how leads.stage is stored — string column, validated in the app.
     */
    public function up(): void
    {
        // On Postgres the enum is backed by a CHECK constraint that would reject
        // "completed"; drop it before widening the column to a plain string.
        if (DB::getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE reservations DROP CONSTRAINT IF EXISTS reservations_status_check');
        }

        Schema::table('reservations', function (Blueprint $table) {
            $table->string('status')->default('pending')->change();
        });
    }

    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])
                ->default('pending')
                ->change();
        });
    }
};
