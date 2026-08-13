<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Filament\Resources\Artists\ArtistResource;
use App\Models\EventParticipant;
use App\Models\Stage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $title = 'Artists';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('artist_id')
                    ->label('Artist')
                    ->relationship('artist', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Artists are stored separately and can be reused on other events.')
                    ->unique(
                        ignoreRecord: true,
                        modifyRuleUsing: fn (Unique $rule): Unique => $rule->where(
                            'event_id',
                            $this->getOwnerRecord()->getKey(),
                        ),
                    )
                    ->createOptionForm(ArtistResource::formComponents()),
                Repeater::make('scheduleEntries')
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
                        DateTimePicker::make('starts_at')
                            ->label('Starts')
                            ->required()
                            ->native(false)
                            ->seconds(false),
                        DateTimePicker::make('ends_at')
                            ->label('Ends')
                            ->native(false)
                            ->seconds(false)
                            ->after('starts_at'),
                        Textarea::make('notes')
                            ->rows(2)
                            ->columnSpanFull(),
                    ])
                    ->columns(3)
                    ->defaultItems(0)
                    ->collapsible()
                    ->itemLabel(function (array $state): ?string {
                        if (blank($state['starts_at'] ?? null)) {
                            return 'Schedule entry';
                        }

                        $stageName = filled($state['stage_id'] ?? null)
                            ? Stage::query()->find($state['stage_id'])?->name
                            : null;

                        $label = (string) $state['starts_at'];

                        if ($stageName) {
                            $label .= ' — '.$stageName;
                        }

                        return $label;
                    })
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('artist.name')
            ->columns([
                ImageColumn::make('artist.image_path')
                    ->label('Photo')
                    ->getStateUsing(fn (EventParticipant $record): ?string => $record->artist?->imageUrl())
                    ->circular(),
                TextColumn::make('artist.name')
                    ->label('Artist')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('artist.participantType.name')
                    ->label('Type')
                    ->sortable(),
                TextColumn::make('artist.genres.name')
                    ->label('Genres')
                    ->badge()
                    ->separator(',')
                    ->placeholder('—'),
                TextColumn::make('schedule_entries_count')
                    ->counts('scheduleEntries')
                    ->label('Schedule'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make()
                    ->label('Add artist'),
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
