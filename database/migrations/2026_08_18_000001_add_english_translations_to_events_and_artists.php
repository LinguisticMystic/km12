<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->text('description_en')->nullable()->after('description');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->text('bio_en')->nullable()->after('bio');
        });

        DB::table('events')->whereNotNull('description')->update([
            'description_en' => DB::raw('description'),
        ]);

        DB::table('artists')->whereNotNull('bio')->update([
            'bio_en' => DB::raw('bio'),
        ]);
    }

    public function down(): void
    {
        Schema::table('events', function (Blueprint $table) {
            $table->dropColumn('description_en');
        });

        Schema::table('artists', function (Blueprint $table) {
            $table->dropColumn('bio_en');
        });
    }
};
