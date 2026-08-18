<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('extra_types', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('extras', function (Blueprint $table) {
            $table->id();
            $table->foreignId('extra_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->text('bio_en')->nullable();
            $table->string('instagram_url')->nullable();
            $table->string('website_url')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::table('event_participants', function (Blueprint $table) {
            $table->foreignId('extra_id')
                ->nullable()
                ->after('artist_id')
                ->constrained()
                ->restrictOnDelete();

            $table->unique(['event_id', 'extra_id']);
        });

        Schema::table('event_participants', function (Blueprint $table) {
            $table->unsignedBigInteger('artist_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropUnique(['event_id', 'extra_id']);
            $table->dropConstrainedForeignId('extra_id');
        });

        Schema::table('event_participants', function (Blueprint $table) {
            $table->unsignedBigInteger('artist_id')->nullable(false)->change();
        });

        Schema::dropIfExists('extras');
        Schema::dropIfExists('extra_types');
    }
};
