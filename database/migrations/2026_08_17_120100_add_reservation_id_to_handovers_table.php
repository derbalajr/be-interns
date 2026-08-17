<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Link each handover to the reservation it fulfils. Nullable because
     * handovers created before this change have no reservation on record;
     * new handovers always set it (derived from the sold unit's reservation).
     */
    public function up(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->foreignId('reservation_id')
                ->nullable()
                ->after('unit_id')
                ->constrained()
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('handovers', function (Blueprint $table) {
            $table->dropConstrainedForeignId('reservation_id');
        });
    }
};
