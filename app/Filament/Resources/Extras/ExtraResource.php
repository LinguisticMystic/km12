<?php

namespace App\Filament\Resources\Extras;

use App\Filament\Resources\Extras\Pages\CreateExtra;
use App\Filament\Resources\Extras\Pages\EditExtra;
use App\Filament\Resources\Extras\Pages\ListExtras;
use App\Models\Extra;
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

class ExtraResource extends Resource
{
    protected static ?string $model = Extra::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedSparkles;

    protected static ?string $navigationLabel = 'Extras';

    protected static string|\UnitEnum|null $navigationGroup = 'Extras';

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
            Select::make('extra_type_id')
                ->label('Type')
                ->relationship('extraType', 'name')
                ->required()
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
            TextInput::make('instagram_url')
                ->label('Instagram URL')
                ->url()
                ->maxLength(255)
                ->nullable()
                ->placeholder('https://instagram.com/...'),
            TextInput::make('website_url')
                ->label('Website URL')
                ->url()
                ->maxLength(255)
                ->nullable()
                ->placeholder('https://'),
            FileUpload::make('image_path')
                ->label('Profile image')
                ->image()
                ->disk('public')
                ->directory('extras')
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
                    ->getStateUsing(fn (Extra $record): ?string => $record->imageUrl())
                    ->circular(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('extraType.name')
                    ->label('Type')
                    ->sortable(),
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
            'index' => ListExtras::route('/'),
            'create' => CreateExtra::route('/create'),
            'edit' => EditExtra::route('/{record}/edit'),
        ];
    }
}
