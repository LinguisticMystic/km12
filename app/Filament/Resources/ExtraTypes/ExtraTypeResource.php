<?php

namespace App\Filament\Resources\ExtraTypes;

use App\Filament\Resources\ExtraTypes\Pages\CreateExtraType;
use App\Filament\Resources\ExtraTypes\Pages\EditExtraType;
use App\Filament\Resources\ExtraTypes\Pages\ListExtraTypes;
use App\Models\ExtraType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ExtraTypeResource extends Resource
{
    protected static ?string $model = ExtraType::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Extra types';

    protected static string|\UnitEnum|null $navigationGroup = 'Extras';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'extra type';

    protected static ?string $pluralModelLabel = 'extra types';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('extras_count')
                    ->counts('extras')
                    ->label('Extras'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
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
            'index' => ListExtraTypes::route('/'),
            'create' => CreateExtraType::route('/create'),
            'edit' => EditExtraType::route('/{record}/edit'),
        ];
    }
}
