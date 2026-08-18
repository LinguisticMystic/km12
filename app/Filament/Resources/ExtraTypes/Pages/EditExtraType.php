<?php

namespace App\Filament\Resources\ExtraTypes\Pages;

use App\Filament\Resources\ExtraTypes\ExtraTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditExtraType extends EditRecord
{
    protected static string $resource = ExtraTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
