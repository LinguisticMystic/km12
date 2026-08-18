<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Forms\ScheduleEntriesRepeater;
use App\Filament\Resources\Extras\ExtraResource;
use App\Models\EventParticipant;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ExtrasRelationManager extends RelationManager
{
    protected static string $relationship = 'extraParticipants';

    protected static ?string $title = 'Extras';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('extra_id')
                    ->label('Extra')
                    ->relationship('extra', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Extras are stored separately and can be reused on other events.')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where(
                            'event_id',
                            $this->getOwnerRecord()->getKey(),
                        ),
                    )
                    ->createOptionForm(ExtraResource::formComponents()),
                ScheduleEntriesRepeater::make($this->getOwnerRecord()->date),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('extra.name')
            ->columns([
                ImageColumn::make('extra.image_path')
                    ->label('Photo')
                    ->getStateUsing(fn (EventParticipant $record): ?string => $record->extra?->imageUrl())
                    ->circular(),
                TextColumn::make('extra.name')
                    ->label('Extra')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('extra.extraType.name')
                    ->label('Type')
                    ->sortable(),
                TextColumn::make('schedule_entries_count')
                    ->counts('scheduleEntries')
                    ->label('Schedule'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label('Add extra'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
