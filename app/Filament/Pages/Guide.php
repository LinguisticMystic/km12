<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Text;
use Filament\Schemas\Components\UnorderedList;
use Filament\Schemas\Schema;
use Filament\Support\Enums\Width;
use Filament\Support\Icons\Heroicon;

class Guide extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = Heroicon::OutlinedBookOpen;

    protected static ?string $navigationLabel = 'Guide';

    protected static string|\UnitEnum|null $navigationGroup = 'Events';

    protected static ?int $navigationSort = 3;

    protected static ?string $title = 'Events guide';

    protected ?string $subheading = 'Short steps. Skip what you already know.';

    public function getMaxContentWidth(): Width
    {
        return Width::Prose;
    }

    public function content(Schema $schema): Schema
    {
        return $schema
            ->components([
                Callout::make('Artists and extras live in their own lists.')
                    ->description('An event is just a date, a poster, and who you attach. You can reuse the same person on many events.')
                    ->info(),
                Section::make('1. Make the event')
                    ->compact()
                    ->schema([
                        UnorderedList::make([
                            Text::make('Left menu: Events.'),
                            Text::make('Top right: create.'),
                            Text::make('Fill name, date, and Description (LV).'),
                            Text::make('Optional: English description, ticket link, poster.'),
                            Text::make('Save.'),
                        ])->columns(['sm' => 1, 'lg' => 1]),
                        Text::make('You cannot add people until it is saved. Open the event again (edit).'),
                    ]),
                Section::make('2. Put an artist on it')
                    ->compact()
                    ->schema([
                        Text::make('On the event edit page, scroll to Artists.'),
                        UnorderedList::make([
                            Text::make('Click Add artist.'),
                            Text::make('Pick them from the list. New person? Use + on that same field. Name + type are enough. Save them, then they are selected.'),
                            Text::make('Optional: add schedule (stage, date, start/end). Skip if you do not know times yet.'),
                            Text::make('Create / save.'),
                        ])->columns(['sm' => 1, 'lg' => 1]),
                        Text::make('Do this once per artist. Same artist cannot be added twice to the same event.'),
                        Text::make('Drag the rows to change the order on the website.'),
                    ]),
                Section::make('3. Put an extra on it')
                    ->compact()
                    ->schema([
                        Text::make('Same as artists, but the block is Extras → Add extra.'),
                    ]),
                Section::make('Later')
                    ->compact()
                    ->schema([
                        UnorderedList::make([
                            Text::make('Change times: click the person on the event → edit → save.'),
                            Text::make('Take them off this event only: delete on the event (the artist/extra themselves stay in the lists).'),
                            Text::make('Fix a name/photo/bio: Artists or Extras in the left menu — that updates every event they are on.'),
                        ])->columns(['sm' => 1, 'lg' => 1]),
                    ]),
            ]);
    }
}
