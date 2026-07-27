<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('leads')
            ->whereNull('stage')
            ->update([
                'stage' => 'new',
            ]);

        Schema::table('leads', function (Blueprint $table) {
            $table->string('stage')
                ->default('new')
                ->change();
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->string('stage')
                ->default(null)
                ->change();
        });
    }
};
