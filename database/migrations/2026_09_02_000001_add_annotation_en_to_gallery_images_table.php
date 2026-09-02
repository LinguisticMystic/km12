<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->string('annotation_en')->nullable()->after('annotation');
        });

        DB::table('gallery_images')->whereNotNull('annotation')->update([
            'annotation_en' => DB::raw('annotation'),
        ]);
    }

    public function down(): void
    {
        Schema::table('gallery_images', function (Blueprint $table) {
            $table->dropColumn('annotation_en');
        });
    }
};
