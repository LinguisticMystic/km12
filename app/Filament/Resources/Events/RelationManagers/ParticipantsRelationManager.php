<?php

namespace App\Filament\Resources\Events\RelationManagers;

use App\Models\EventParticipant;
use App\Models\Stage;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipantsRelationManager extends RelationManager
{
    protected static string $relationship = 'participants';

    protected static ?string $title = 'Participants';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Select::make('participant_type_id')
                    ->label('Type')
                    ->relationship('participantType', 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Textarea::make('bio')
                    ->label('Short bio')
                    ->rows(4)
                    ->columnSpanFull(),
                FileUpload::make('image_path')
                    ->label('Profile image')
                    ->image()
                    ->disk('public')
                    ->directory('participants')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->automaticallyResizeImagesMode('contain')
                    ->automaticallyResizeImagesToWidth(800)
                    ->automaticallyResizeImagesToHeight(800)
                    ->imageResizeUpscale(false)
                    ->columnSpanFull(),
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
            ->recordTitleAttribute('name')
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Photo')
                    ->getStateUsing(fn (EventParticipant $record): ?string => $record->imageUrl())
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('participantType.name')
                    ->label('Type')
                    ->sortable(),
                TextColumn::make('schedule_entries_count')
                    ->counts('scheduleEntries')
                    ->label('Schedule'),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                CreateAction::make(),
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
