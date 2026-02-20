<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\BeritaResource\Pages;
use App\Filament\Admin\Resources\BeritaResource\RelationManagers;
use App\Models\Berita;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BeritaResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-newspaper';

    protected static ?string $navigationGroup = 'PUBLIKASI';

    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isOperatorKecamatan();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Konten Berita')
                    ->schema([
                        Forms\Components\TextInput::make('judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn(string $operation, $state, Forms\Set $set) => $operation === 'create' ? $set('slug', \Illuminate\Support\Str::slug($state)) : null),
                        Forms\Components\TextInput::make('slug')
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->unique(Berita::class, 'slug', ignoreRecord: true),
                        Forms\Components\Select::make('kategori')
                            ->options([
                                'Pemerintahan' => 'Pemerintahan',
                                'Ekbang' => 'Ekbang',
                                'Kesra' => 'Kesejahteraan Rakyat',
                                'Trantibum' => 'Trantibum',
                                'Umum' => 'Umum',
                            ])
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                'draft' => 'Draft',
                                'published' => 'Published',
                                'archived' => 'Archived',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\DateTimePicker::make('published_at'),
                        Forms\Components\Select::make('author_id')
                            ->relationship('author', 'nama_lengkap')
                            ->required()
                            ->default(auth()->id())
                            ->searchable(),
                        Forms\Components\Textarea::make('ringkasan')
                            ->columnSpanFull(),
                        Forms\Components\RichEditor::make('konten')
                            ->required()
                            ->columnSpanFull()
                            ->fileAttachmentsDirectory('berita/konten'),
                        Forms\Components\FileUpload::make('thumbnail')
                            ->image()
                            ->directory('berita/thumbnails')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('thumbnail')
                    ->circular(),
                Tables\Columns\TextColumn::make('judul')
                    ->searchable()
                    ->sortable()
                    ->limit(50),
                Tables\Columns\TextColumn::make('kategori')
                    ->badge(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'published' => 'success',
                        'draft' => 'warning',
                        'archived' => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('author.nama_lengkap')
                    ->label('Penulis')
                    ->sortable(),
                Tables\Columns\TextColumn::make('published_at')
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'draft' => 'Draft',
                        'published' => 'Published',
                        'archived' => 'Archived',
                    ]),
                Tables\Filters\SelectFilter::make('kategori')
                    ->options([
                        'Pemerintahan' => 'Pemerintahan',
                        'Ekbang' => 'Ekbang',
                        'Kesra' => 'Kesejahteraan Rakyat',
                        'Trantibum' => 'Trantibum',
                        'Umum' => 'Umum',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBeritas::route('/'),
            'create' => Pages\CreateBerita::route('/create'),
            'edit' => Pages\EditBerita::route('/{record}/edit'),
        ];
    }
}
