<?php

namespace App\Filament\Resources\Quests\Pages;

use App\Filament\Resources\Quests\QuestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateQuest extends CreateRecord
{
    protected static string $resource = QuestResource::class;

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::id();

        return $data;
    }
}
