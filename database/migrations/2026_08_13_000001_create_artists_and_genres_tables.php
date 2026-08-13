<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('genres', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        Schema::create('artists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('participant_type_id')->constrained()->restrictOnDelete();
            $table->string('name');
            $table->text('bio')->nullable();
            $table->string('image_path')->nullable();
            $table->timestamps();
        });

        Schema::create('artist_genre', function (Blueprint $table) {
            $table->foreignId('artist_id')->constrained()->cascadeOnDelete();
            $table->foreignId('genre_id')->constrained()->cascadeOnDelete();

            $table->primary(['artist_id', 'genre_id']);
        });

        Schema::table('event_participants', function (Blueprint $table) {
            $table->foreignId('artist_id')
                ->nullable()
                ->after('event_id')
                ->constrained()
                ->restrictOnDelete();
        });

        $now = now();

        foreach (DB::table('event_participants')->orderBy('id')->get() as $participant) {
            $artistId = DB::table('artists')->insertGetId([
                'participant_type_id' => $participant->participant_type_id,
                'name' => $participant->name,
                'bio' => $participant->bio,
                'image_path' => $participant->image_path,
                'created_at' => $participant->created_at ?? $now,
                'updated_at' => $participant->updated_at ?? $now,
            ]);

            DB::table('event_participants')
                ->where('id', $participant->id)
                ->update(['artist_id' => $artistId]);
        }

        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('participant_type_id');
            $table->dropColumn(['name', 'bio', 'image_path']);
        });

        Schema::table('event_participants', function (Blueprint $table) {
            $table->unsignedBigInteger('artist_id')->nullable(false)->change();
            $table->unique(['event_id', 'artist_id']);
        });
    }

    public function down(): void
    {
        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropUnique(['event_id', 'artist_id']);
            $table->foreignId('participant_type_id')->nullable()->after('event_id')->constrained()->restrictOnDelete();
            $table->string('name')->nullable();
            $table->text('bio')->nullable();
            $table->string('image_path')->nullable();
        });

        foreach (DB::table('event_participants')->orderBy('id')->get() as $participant) {
            $artist = DB::table('artists')->where('id', $participant->artist_id)->first();

            if ($artist === null) {
                continue;
            }

            DB::table('event_participants')
                ->where('id', $participant->id)
                ->update([
                    'participant_type_id' => $artist->participant_type_id,
                    'name' => $artist->name,
                    'bio' => $artist->bio,
                    'image_path' => $artist->image_path,
                ]);
        }

        Schema::table('event_participants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('artist_id');
        });

        Schema::table('event_participants', function (Blueprint $table) {
            $table->string('name')->nullable(false)->change();
            $table->unsignedBigInteger('participant_type_id')->nullable(false)->change();
        });

        Schema::dropIfExists('artist_genre');
        Schema::dropIfExists('artists');
        Schema::dropIfExists('genres');
    }
};
