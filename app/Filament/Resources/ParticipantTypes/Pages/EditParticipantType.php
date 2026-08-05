<?php

namespace App\Filament\Resources\ParticipantTypes\Pages;

use App\Filament\Resources\ParticipantTypes\ParticipantTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditParticipantType extends EditRecord
{
    protected static string $resource = ParticipantTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
