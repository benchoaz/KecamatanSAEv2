<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\UmkmResource\Pages;
use App\Filament\Admin\Resources\UmkmResource\RelationManagers;
use App\Models\Umkm;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class UmkmResource extends Resource
{
    protected static ?string $model = Umkm::class;

    protected static ?string $navigationIcon = 'heroicon-o-building-storefront';

    protected static ?string $navigationLabel = 'UMKM Rakyat';

    protected static ?string $modelLabel = 'UMKM Rakyat';

    protected static ?string $pluralModelLabel = 'UMKM Rakyat';

    protected static ?string $navigationGroup = null; // Top level as requested

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Usaha')
                    ->schema([
                        Forms\Components\TextInput::make('nama_usaha')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('nama_pemilik')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\TextInput::make('no_wa')
                            ->required()
                            ->tel()
                            ->maxLength(255),
                        Forms\Components\Select::make('desa')
                            ->required()
                            ->options(\App\Models\Desa::pluck('nama_desa', 'nama_desa'))
                            ->searchable(),
                        Forms\Components\TextInput::make('jenis_usaha')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Textarea::make('deskripsi')
                            ->maxLength(65535)
                            ->columnSpanFull(),
                    ])->columns(2),

                Forms\Components\Section::make('Status & Teknis')
                    ->schema([
                        Forms\Components\Select::make('status')
                            ->options([
                                'pending' => 'Pending Verification',
                                'aktif' => 'Aktif',
                                'nonaktif' => 'Non-Aktif',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\Select::make('source')
                            ->options([
                                'admin' => 'Admin Input',
                                'self-service' => 'Self Service',
                            ])
                            ->required(),
                        Forms\Components\FileUpload::make('foto_usaha')
                            ->image()
                            ->directory('umkm/usaha'),
                        Forms\Components\TextInput::make('manage_token')
                            ->disabled()
                            ->label('Token Kelola (Secret)'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('foto_usaha')
                    ->circular(),
                Tables\Columns\TextColumn::make('nama_usaha')
                    ->searchable()
                    ->sortable()
                    ->description(fn(Umkm $record) => $record->jenis_usaha),
                Tables\Columns\TextColumn::make('nama_pemilik')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_wa')
                    ->label('WhatsApp')
                    ->url(fn(Umkm $record) => "https://wa.me/" . preg_replace('/[^0-9]/', '', $record->no_wa), true),
                Tables\Columns\TextColumn::make('desa')
                    ->badge()
                    ->color('gray')
                    ->sortable(),
                Tables\Columns\SelectColumn::make('status')
                    ->options([
                        'pending' => 'Pending',
                        'aktif' => 'Aktif',
                        'nonaktif' => 'Non-Aktif',
                    ])
                    ->sortable(),
                Tables\Columns\TextColumn::make('source')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'admin' => 'info',
                        'self-service' => 'success',
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status'),
                Tables\Filters\SelectFilter::make('desa')
                    ->options(\App\Models\Desa::pluck('nama_desa', 'nama_desa')),
            ])
            ->actions([
                Tables\Actions\Action::make('verify')
                    ->label('Verifikasi')
                    ->icon('heroicon-o-check-badge')
                    ->color('success')
                    ->hidden(fn(Umkm $record) => $record->status === 'aktif')
                    ->action(function (Umkm $record) {
                        $record->update(['status' => 'aktif']);
                        \App\Models\UmkmAdminLog::create([
                            'umkm_id' => $record->id,
                            'action' => 'verify',
                            'actor' => 'admin',
                            'notes' => 'Verified by admin via dashboard'
                        ]);
                    }),
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
            RelationManagers\ProductsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUmkms::route('/'),
            'create' => Pages\CreateUmkm::route('/create'),
            'edit' => Pages\EditUmkm::route('/{record}/edit'),
        ];
    }
}
