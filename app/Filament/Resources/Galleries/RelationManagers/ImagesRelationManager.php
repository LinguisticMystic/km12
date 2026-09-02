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
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;

class ImagesRelationManager extends RelationManager
{
    protected static string $relationship = 'images';

    protected static ?string $title = 'Images';

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('annotation')
                    ->label('Annotation (LV)')
                    ->helperText('Photographer credit or a short note.')
                    ->maxLength(255)
                    ->columnSpanFull(),
                TextInput::make('annotation_en')
                    ->label('Annotation (EN)')
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
                    ->label('Annotation (LV)')
                    ->placeholder('—')
                    ->searchable()
                    ->wrap(),
                TextColumn::make('annotation_en')
                    ->label('Annotation (EN)')
                    ->placeholder('—')
                    ->searchable()
                    ->wrap()
                    ->toggleable(),
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
                            ->storeFiles(false)
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
                            ->label('Annotation (LV)')
                            ->helperText('Applied to every image in this upload. Leave blank to annotate later.')
                            ->maxLength(255),
                        TextInput::make('annotation_en')
                            ->label('Annotation (EN)')
                            ->maxLength(255),
                    ])
                    ->action(function (array $data): void {
                        /** @var Gallery $gallery */
                        $gallery = $this->getOwnerRecord();
                        $directory = 'galleries/'.$gallery->getKey();
                        $disk = Storage::disk('public');
                        $disk->makeDirectory($directory);

                        $directoryPath = $disk->path($directory);

                        if (is_dir($directoryPath)) {
                            @chmod($directoryPath, 0777);
                        }

                        $sort = (int) ($gallery->images()->max('sort_order') ?? 0);
                        $annotation = filled($data['annotation'] ?? null) ? $data['annotation'] : null;
                        $annotationEn = filled($data['annotation_en'] ?? null) ? $data['annotation_en'] : null;

                        foreach (Arr::wrap($data['images'] ?? []) as $file) {
                            $path = null;

                            if ($file instanceof TemporaryUploadedFile) {
                                $path = $file->store($directory, 'public');
                            } elseif (is_string($file) && filled($file)) {
                                $path = $file;
                            }

                            if (! is_string($path) || $path === '' || ! $disk->exists($path)) {
                                continue;
                            }

                            $gallery->images()->create([
                                'path' => $path,
                                'annotation' => $annotation,
                                'annotation_en' => $annotationEn,
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
                                ->label('Annotation (LV)')
                                ->helperText('Applied to all selected images. Clear the field to remove the annotation.')
                                ->maxLength(255),
                            TextInput::make('annotation_en')
                                ->label('Annotation (EN)')
                                ->maxLength(255),
                        ])
                        ->action(function (Collection $records, array $data): void {
                            $records->each->update([
                                'annotation' => filled($data['annotation'] ?? null) ? $data['annotation'] : null,
                                'annotation_en' => filled($data['annotation_en'] ?? null) ? $data['annotation_en'] : null,
                            ]);
                        })
                        ->deselectRecordsAfterCompletion(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
