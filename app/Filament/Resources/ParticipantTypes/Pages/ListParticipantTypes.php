<?php

namespace App\Filament\Resources\ParticipantTypes\Pages;

use App\Filament\Resources\ParticipantTypes\ParticipantTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListParticipantTypes extends ListRecords
{
    protected static string $resource = ParticipantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
