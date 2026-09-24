<?php

namespace App\Filament\Resources\Quests;

use App\Enums\QuestStatus;
use App\Filament\Resources\Quests\Pages\CreateQuest;
use App\Filament\Resources\Quests\Pages\EditQuest;
use App\Filament\Resources\Quests\Pages\ListQuests;
use App\Models\Quest;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;

class QuestResource extends Resource
{
    protected static ?string $model = Quest::class;

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedFlag;

    protected static ?string $navigationLabel = 'Quests';

    protected static string|\UnitEnum|null $navigationGroup = 'Quests';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Textarea::make('description')
                    ->required()
                    ->rows(5)
                    ->columnSpanFull(),
                TextInput::make('experience_points')
                    ->label('Experience points')
                    ->numeric()
                    ->integer()
                    ->minValue(0)
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('experience_points')
                    ->label('XP')
                    ->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->formatStateUsing(fn (QuestStatus $state): string => $state->label())
                    ->sortable(),
                TextColumn::make('taker.name')
                    ->label('Taken by')
                    ->placeholder('—')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label('Created by')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                Action::make('complete')
                    ->label('Complete')
                    ->requiresConfirmation()
                    ->visible(fn (Quest $record): bool => $record->status === QuestStatus::Submitted)
                    ->action(function (Quest $record): void {
                        Gate::authorize('complete', $record);
                        $record->complete();
                    }),
                Action::make('reject')
                    ->label('Reject')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (Quest $record): bool => $record->status === QuestStatus::Submitted)
                    ->action(function (Quest $record): void {
                        Gate::authorize('reject', $record);
                        $record->reject();
                    }),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * @return Builder<Quest>
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->with(['creator', 'taker']);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListQuests::route('/'),
            'create' => CreateQuest::route('/create'),
            'edit' => EditQuest::route('/{record}/edit'),
        ];
    }
}
