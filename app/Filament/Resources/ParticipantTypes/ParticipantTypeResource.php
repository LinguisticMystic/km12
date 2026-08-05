<?php

namespace App\Filament\Resources\ParticipantTypes;

use App\Filament\Resources\ParticipantTypes\Pages\CreateParticipantType;
use App\Filament\Resources\ParticipantTypes\Pages\EditParticipantType;
use App\Filament\Resources\ParticipantTypes\Pages\ListParticipantTypes;
use App\Models\ParticipantType;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ParticipantTypeResource extends Resource
{
    protected static ?string $model = ParticipantType::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedTag;

    protected static ?string $navigationLabel = 'Participant types';

    protected static string|\UnitEnum|null $navigationGroup = 'Events';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'participant type';

    protected static ?string $pluralModelLabel = 'participant types';

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
            'index' => ListParticipantTypes::route('/'),
            'create' => CreateParticipantType::route('/create'),
            'edit' => EditParticipantType::route('/{record}/edit'),
        ];
    }
}
