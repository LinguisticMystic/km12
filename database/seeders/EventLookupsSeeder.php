<?php

namespace Database\Seeders;

use App\Models\Genre;
use App\Models\ParticipantType;
use App\Models\Stage;
use Illuminate\Database\Seeder;

class EventLookupsSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            'DJ',
            'Live musician',
            'Performer',
            'Acrobat',
            'Caterer',
            'Workshop',
            'Installation',
            'Volunteer',
        ];

        foreach ($types as $index => $name) {
            ParticipantType::query()->updateOrCreate(
                ['name' => $name],
                ['sort_order' => $index],
            );
        }

        $genres = [
            'Ambient',
            'Disco',
            'Drum & Bass',
            'Experimental',
            'Funk',
            'Hip Hop',
            'House',
            'Jazz',
            'Live',
            'Psychedelic',
            'Techno',
            'Trance',
            'World',
        ];

        foreach ($genres as $index => $name) {
            Genre::query()->updateOrCreate(
                ['name' => $name],
                ['sort_order' => $index],
            );
        }

        $stages = [
            'Main Stage',
            'Forest Stage',
            'Tea Tent',
            'Workshop Area',
            'Chill Zone',
            'Kitchen',
        ];

        foreach ($stages as $index => $name) {
            Stage::query()->updateOrCreate(
                ['name' => $name],
                ['sort_order' => $index],
            );
        }
    }
}
