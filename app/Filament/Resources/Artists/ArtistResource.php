<?php

namespace App\Filament\Resources\Artists;

use App\Filament\Resources\Artists\Pages\CreateArtist;
use App\Filament\Resources\Artists\Pages\EditArtist;
use App\Filament\Resources\Artists\Pages\ListArtists;
use App\Models\Artist;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ArtistResource extends Resource
{
    protected static ?string $model = Artist::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedMusicalNote;

    protected static ?string $navigationLabel = 'Artists';

    protected static string|\UnitEnum|null $navigationGroup = 'Artists';

    protected static ?int $navigationSort = 1;

    /**
     * @return array<int, Component>
     */
    public static function formComponents(): array
    {
        return [
            TextInput::make('name')
                ->required()
                ->maxLength(255),
            Select::make('participant_type_id')
                ->label('Type')
                ->relationship('participantType', 'name')
                ->required()
                ->searchable()
                ->preload(),
            Select::make('genres')
                ->relationship('genres', 'name')
                ->multiple()
                ->searchable()
                ->preload()
                ->createOptionForm([
                    TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->unique(),
                ]),
            Textarea::make('bio')
                ->label('Short bio (LV)')
                ->rows(4)
                ->columnSpanFull(),
            Textarea::make('bio_en')
                ->label('Short bio (EN)')
                ->rows(4)
                ->columnSpanFull(),
            FileUpload::make('image_path')
                ->label('Profile image')
                ->image()
                ->disk('public')
                ->directory('artists')
                ->visibility('public')
                ->maxSize(2048)
                ->automaticallyResizeImagesMode('contain')
                ->automaticallyResizeImagesToWidth(800)
                ->automaticallyResizeImagesToHeight(800)
                ->imageResizeUpscale(false)
                ->columnSpanFull(),
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components(self::formComponents());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('image_path')
                    ->label('Photo')
                    ->getStateUsing(fn (Artist $record): ?string => $record->imageUrl())
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('participantType.name')
                    ->label('Type')
                    ->sortable(),
                TextColumn::make('genres.name')
                    ->badge()
                    ->separator(',')
                    ->placeholder('—'),
                TextColumn::make('events_count')
                    ->counts('events')
                    ->label('Events'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArtists::route('/'),
            'create' => CreateArtist::route('/create'),
            'edit' => EditArtist::route('/{record}/edit'),
        ];
    }
}
