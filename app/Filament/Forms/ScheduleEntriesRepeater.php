<?php

namespace App\Filament\Forms;

use App\Models\Stage;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;

class ScheduleEntriesRepeater
{
    public static function make(mixed $defaultDate = null): Repeater
    {
        return Repeater::make('scheduleEntries')
            ->relationship()
            ->label('Schedule entries')
            ->schema([
                Select::make('stage_id')
                    ->label('Stage / place')
                    ->options(fn (): array => Stage::query()
                        ->orderBy('sort_order')
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->required()
                    ->searchable(),
                DatePicker::make('date')
                    ->required()
                    ->native(false)
                    ->default($defaultDate),
                TimePicker::make('starts_at')
                    ->label('Starts')
                    ->seconds(false)
                    ->nullable()
                    ->helperText('Leave empty if there is no set start time.'),
                TimePicker::make('ends_at')
                    ->label('Ends')
                    ->seconds(false)
                    ->nullable(),
                Textarea::make('notes')
                    ->label('Notes (LV)')
                    ->rows(2)
                    ->columnSpanFull(),
                Textarea::make('notes_en')
                    ->label('Notes (EN)')
                    ->rows(2)
                    ->columnSpanFull(),
            ])
            ->columns(2)
            ->defaultItems(0)
            ->collapsible()
            ->itemLabel(function (array $state): ?string {
                $stageName = filled($state['stage_id'] ?? null)
                    ? Stage::query()->find($state['stage_id'])?->name
                    : null;

                $label = filled($state['date'] ?? null)
                    ? (string) $state['date']
                    : 'Schedule entry';

                if (filled($state['starts_at'] ?? null)) {
                    $label .= ' '.$state['starts_at'];
                }

                if ($stageName) {
                    $label .= ' — '.$stageName;
                }

                return $label;
            })
            ->columnSpanFull();
    }
}
