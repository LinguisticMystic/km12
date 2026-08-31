<?php

namespace App\Filament\Resources\Galleries\RelationManagers;

use App\Models\Gallery;
use App\Models\GalleryImage;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Images';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('annotation')
                    ->label('Annotation')
                    ->helperText('Photographer credit or a short note.')
                    ->maxLength(255)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        $galleryId = $this->getOwnerRecord()->getKey();

        return $table
            ->recordTitleAttribute('annotation')
            ->columns([
                ImageColumn::make('path')
                    ->label('Image')
                    ->getStateUsing(fn (GalleryImage $record): ?string => $record->url())
                    ->square(),
                TextColumn::make('annotation')
                    ->label('Annotation')
                    ->placeholder('—')
                    ->searchable()
                    ->wrap(),
            ])
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->headerActions([
                Action::make('uploadImages')
                    ->label('Upload images')
                    ->icon(Heroicon::OutlinedArrowUpTray)
                    ->schema([
                        FileUpload::make('images')
                            ->label('Images')
                            ->multiple()
                            ->image()
                            ->disk('public')
                            ->directory('galleries/'.$galleryId)
                            ->visibility('public')
                            ->maxSize(12288)
                            ->automaticallyResizeImagesMode('contain')
                            ->automaticallyResizeImagesToWidth(1600)
                            ->automaticallyResizeImagesToHeight(1600)
                            ->imageResizeUpscale(false)
                            ->panelLayout('grid')
                            ->reorderable()
                            ->required()
                            ->helperText('Images are resized automatically. Camera files up to 12 MB are fine.')
                            ->columnSpanFull(),
                        TextInput::make('annotation')
                            ->label('Annotation')
                            ->helperText('Applied to every image in this upload. Leave blank to annotate later.')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        /** @var Gallery $gallery */
                        $gallery = $this->getOwnerRecord();
                        $sort = (int) ($gallery->images()->max('sort_order') ?? 0);
                        $annotation = filled($data['annotation'] ?? null) ? $data['annotation'] : null;

                        foreach ($data['images'] ?? [] as $path) {
                            if (! filled($path) || ! is_string($path)) {
                                continue;
                            }

                            $gallery->images()->create([
                                'path' => $path,
                                'annotation' => $annotation,
                                'sort_order' => ++$sort,
                            ]);
                        }
                    }),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('setAnnotation')
                        ->label('Set annotation')
                        ->icon(Heroicon::OutlinedPencilSquare)
                        ->schema([
                            TextInput::make('annotation')
                                ->label('Annotation')
                                ->helperText('Applied to all selected images. Clear the field to remove the annotation.')
                                ->maxLength(255),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $annotation = filled($data['annotation'] ?? null) ? $data['annotation'] : null;

                            $records->each->update(['annotation' => $annotation]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
