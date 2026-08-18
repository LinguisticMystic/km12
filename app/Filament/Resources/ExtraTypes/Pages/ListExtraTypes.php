<?php

namespace App\Filament\Resources\ExtraTypes\Pages;

use App\Filament\Resources\ExtraTypes\ExtraTypeResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListExtraTypes extends ListRecords
{
    protected static string $resource = ExtraTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
