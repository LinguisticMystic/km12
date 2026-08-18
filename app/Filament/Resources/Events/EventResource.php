<?php

namespace App\Filament\Resources\Events;

use App\Filament\Resources\Events\Pages\CreateEvent;
use App\Filament\Resources\Events\Pages\EditEvent;
use App\Filament\Resources\Events\Pages\ListEvents;
use App\Filament\Resources\Events\RelationManagers\ParticipantsRelationManager;
use App\Models\Event;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class EventResource extends Resource
{
    protected static ?string $model = Event::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedCalendarDays;

    protected static ?string $navigationLabel = 'Events';

    protected static string|\UnitEnum|null $navigationGroup = 'Events';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                DateTimePicker::make('date')
                    ->required()
                    ->native(false)
                    ->seconds(false),
                Textarea::make('description')
                    ->label('Description (LV)')
                    ->required()
                    ->rows(6)
                    ->columnSpanFull(),
                Textarea::make('description_en')
                    ->label('Description (EN)')
                    ->rows(6)
                    ->columnSpanFull(),
                TextInput::make('ticket_url')
                    ->label('Ticket sales URL')
                    ->url()
                    ->maxLength(255)
                    ->nullable(),
                FileUpload::make('poster_path')
                    ->label('Poster')
                    ->image()
                    ->disk('public')
                    ->directory('events')
                    ->visibility('public')
                    ->maxSize(2048)
                    ->automaticallyResizeImagesMode('contain')
                    ->automaticallyResizeImagesToWidth(1200)
                    ->automaticallyResizeImagesToHeight(1200)
                    ->imageResizeUpscale(false)
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('poster_path')
                    ->label('Poster')
                    ->getStateUsing(fn (Event $record): ?string => $record->posterUrl())
                    ->square(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('date')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('ticket_url')
                    ->label('Tickets')
                    ->url(fn (?string $state): ?string => $state)
                    ->openUrlInNewTab()
                    ->limit(40)
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            ParticipantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListEvents::route('/'),
            'create' => CreateEvent::route('/create'),
            'edit' => EditEvent::route('/{record}/edit'),
        ];
    }
}
