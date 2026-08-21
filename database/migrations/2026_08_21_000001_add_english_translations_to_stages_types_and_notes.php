<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('participant_types', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('extra_types', function (Blueprint $table) {
            $table->string('name_en')->nullable()->after('name');
        });

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->text('notes_en')->nullable()->after('notes');
        });

        DB::table('stages')->whereNotNull('name')->update([
            'name_en' => DB::raw('name'),
        ]);

        DB::table('participant_types')->whereNotNull('name')->update([
            'name_en' => DB::raw('name'),
        ]);

        DB::table('extra_types')->whereNotNull('name')->update([
            'name_en' => DB::raw('name'),
        ]);

        DB::table('schedule_entries')->whereNotNull('notes')->update([
            'notes_en' => DB::raw('notes'),
        ]);
    }

    public function down(): void
    {
        Schema::table('stages', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('participant_types', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('extra_types', function (Blueprint $table) {
            $table->dropColumn('name_en');
        });

        Schema::table('schedule_entries', function (Blueprint $table) {
            $table->dropColumn('notes_en');
        });
    }
};
