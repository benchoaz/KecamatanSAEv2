<?php

namespace App\Filament\Admin\Resources;

use App\Filament\Admin\Resources\DesaResource\Pages;
use App\Filament\Admin\Resources\DesaResource\RelationManagers;
use App\Models\Desa;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class DesaResource extends Resource
{
    protected static ?string $navigationIcon = 'heroicon-o-home-modern';

    protected static ?string $navigationGroup = 'MASTER DATA';

    public static function canViewAny(): bool
    {
        return auth()->user()->isSuperAdmin() || auth()->user()->isOperatorKecamatan();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Informasi Desa')
                    ->schema([
                        Forms\Components\TextInput::make('kode_desa')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(20),
                        Forms\Components\TextInput::make('nama_desa')
                            ->required()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('kecamatan')
                            ->default('Besuk')
                            ->required(),
                        Forms\Components\TextInput::make('kabupaten')
                            ->default('Probolinggo')
                            ->required(),
                        Forms\Components\Select::make('status')
                            ->options([
                                Desa::STATUS_AKTIF => 'Aktif',
                                Desa::STATUS_TIDAK_AKTIF => 'Tidak Aktif',
                            ])
                            ->required()
                            ->native(false),
                        Forms\Components\TextInput::make('telepon')
                            ->tel()
                            ->maxLength(20),
                        Forms\Components\TextInput::make('email')
                            ->email()
                            ->maxLength(100),
                        Forms\Components\TextInput::make('website')
                            ->url()
                            ->maxLength(255)
                            ->placeholder('https://namadesa.tatadesa.com'),
                        Forms\Components\Textarea::make('alamat_kantor')
                            ->columnSpanFull(),
                    ])->columns(2)
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('kode_desa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('nama_desa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Desa::STATUS_AKTIF => 'success',
                        Desa::STATUS_TIDAK_AKTIF => 'danger',
                        default => 'gray',
                    }),
                Tables\Columns\TextColumn::make('kecamatan')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('kabupaten')
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('telepon')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('website')
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        Desa::STATUS_AKTIF => 'Aktif',
                        Desa::STATUS_TIDAK_AKTIF => 'Tidak Aktif',
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
            'index' => Pages\ListDesas::route('/'),
            'create' => Pages\CreateDesa::route('/create'),
            'edit' => Pages\EditDesa::route('/{record}/edit'),
        ];
    }
}
