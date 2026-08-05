<?php

namespace App\Filament\Resources\ParticipantTypes\Pages;

use App\Filament\Resources\ParticipantTypes\ParticipantTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateParticipantType extends CreateRecord
{
    protected static string $resource = ParticipantTypeResource::class;
}
