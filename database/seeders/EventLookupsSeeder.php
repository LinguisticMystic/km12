<?php

namespace Database\Seeders;

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
